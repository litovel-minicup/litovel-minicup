<?php

namespace Minicup\Model\Manager;

use LeanMapper\Connection;
use LeanMapper\Exception\InvalidStateException;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Day;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\MatchTerm;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\DayRepository;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\MatchTermRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Minicup\Model\Repository\TeamRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Database\Context;
use Nette\Object;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;

/**
 * provide migration from 2014 minicup db to 2015 db
 * @package Minicup\Model\Manager
 */
class MigrationsManager extends Object
{
    private $teamTablePrefix13 = '2013_tymy_';
    private $matchTablePrefix13 = '2013_zapasy_';

    private $teamTablePrefix = '2014_tymy_';
    private $matchTablePrefix = '2014_zapasy_';

    /** @var CategoryRepository */
    private $CR;

    /** @var TeamRepository */
    private $TR;

    /** @var TeamInfoRepository */
    private $TIR;

    /** @var MatchRepository */
    private $MR;

    /** @var MatchTermRepository */
    private $MTR;

    /** @var DayRepository */
    private $DR;

    /** @var YearRepository */
    private $YR;

    /** @var Context */
    private $context;

    /** @var TeamReplicator */
    private $replicator;

    /** @var TeamDataRefresher */
    private $TDR;

    /** @var MatchManager */
    private $MM;

    /** @var Connection */
    private $connection;

    /**
     * @param CategoryRepository  $CR
     * @param TeamRepository      $TR
     * @param TeamInfoRepository  $TIR
     * @param MatchRepository     $MR
     * @param MatchTermRepository $MTR
     * @param DayRepository       $DR
     * @param YearRepository      $YR
     * @param Context             $con
     * @param TeamReplicator      $replicator
     * @param TeamDataRefresher   $TDR
     * @param MatchManager        $MM
     */
    public function __construct(CategoryRepository $CR,
                                TeamRepository $TR,
                                TeamInfoRepository $TIR,
                                MatchRepository $MR,
                                MatchTermRepository $MTR,
                                DayRepository $DR,
                                YearRepository $YR,
                                Context $con,
                                TeamReplicator $replicator,
                                TeamDataRefresher $TDR,
                                MatchManager $MM,
                                Connection $connection)
    {
        $this->CR = $CR;
        $this->TR = $TR;
        $this->TIR = $TIR;
        $this->MR = $MR;
        $this->MTR = $MTR;
        $this->DR = $DR;
        $this->YR = $YR;
        $this->context = $con;
        $this->replicator = $replicator;
        $this->TDR = $TDR;
        $this->MM = $MM;
        $this->connection = $connection;
    }


    /**
     * @param Category $category
     * @param bool     $truncate
     * @param bool     $withScore
     * @param null     $limit
     * @throws \DibiException
     * @throws \Exception
     */
    public function migrateMatches(Category $category, $truncate = FALSE, $withScore = FALSE, $limit = NULL)
    {
        if ($truncate) {
            $this->truncate($category);
        }
        $year = $this->YR->getSelectedYear();
        $data = $this->context->table($this->matchTablePrefix . $category->slug)->order('cas_odehrani ASC')->limit($limit);
        if ($limit) {
            $data->limit($limit);
        }
        foreach ($data as $row) {
            /** @var DateTime $play_time */
            $play_time = $row->cas_odehrani;
            $play_time->add(\DateInterval::createFromDateString('1 year'));
            $homeName = $row->ref($this->teamTablePrefix . $category->slug, 'ID_domaci')->jmeno;
            $awayName = $row->ref($this->teamTablePrefix . $category->slug, 'ID_hoste')->jmeno;
            $homeSlug = Strings::webalize($homeName);
            $awaySlug = Strings::webalize($awayName);

            $homeTeam = $this->TR->getBySlug($homeSlug, $category);
            $awayTeam = $this->TR->getBySlug($awaySlug, $category);

            if (!$homeTeam) {
                $homeTeam = $this->createTeam($category, $homeName);
            }
            if (!$awayTeam) {
                $awayTeam = $this->createTeam($category, $awayName);
            }
            $match = new Match();
            $match->category = $category;
            $match->homeTeam = $homeTeam->i;
            $match->awayTeam = $awayTeam->i;
            $datetime = new \DibiDateTime($play_time->getTimestamp());

            $matchTerm = $this->MTR->getByStart($datetime);
            if ($matchTerm) {
                $match->matchTerm = $matchTerm;
                $this->MR->persist($match);
            } else {
                $matchTerm = new MatchTerm();
            }
            $day = $this->DR->getByDatetime($datetime);
            if (!$day) {
                $day = new Day();
                $day->year = $year;
                $date = clone $datetime;
                $day->day = $date->setTime(0, 0, 0);
                $this->DR->persist($day);
            }
            $matchTerm->day = $day;
            $start = clone $datetime;
            $start->setDate(0, 0, 0);
            $matchTerm->start = $start;
            $end = clone $datetime;
            $end->setTimestamp((int)$datetime->getTimestamp() + 0.5 * (60 * 60));
            $matchTerm->end = $end;
            $this->MTR->persist($matchTerm);
            $match->matchTerm = $matchTerm;
            $this->MR->persist($match);
            /** @var Category $category */
            $category = $this->CR->get($category->id, FALSE);
            if ($withScore) {
                $this->MM->confirmMatch($match, $category, $row->SCR_domaci, $row->SCR_hoste);
            } else {
                $this->connection->begin();
                try {
                    $category = $match->category;
                    $this->replicator->replicate($category, $match);
                    $this->TDR->refreshData($category);
                } catch (\Exception $e) {
                    $this->connection->rollback();
                    throw $e;
                }
                $this->connection->commit();
            }
        }

    }

    /**
     * @param Category $category
     * @throws  InvalidStateException
     */
    private function truncate(Category $category)
    {
        foreach ($category->matches as $match) {
            $this->MR->delete($match);
        }
        foreach ($category->allTeams as $team) {
            $this->TR->delete($team);
        }
    }

    /**
     * @param Category $category
     * @param string   $name
     * @param string   $slug
     * @return Team
     * @throws \LeanMapper\Exception\InvalidStateException
     */
    private function createTeam(Category $category, $name, $slug = NULL)
    {
        if (!$slug) {
            $slug = Strings::webalize($name);
        }
        $teamInfo = $this->TIR->findByCategoryNameSlug($category, $name, $slug);
        if (!$teamInfo) {
            $teamInfo = new TeamInfo();
            $teamInfo->name = $name;
            $teamInfo->category = $category;
            $teamInfo->slug = $slug;
            $this->TIR->persist($teamInfo);
        }
        $team = new Team;
        $team->i = $teamInfo;
        $team->category = $category;
        $this->TR->persist($team);
        return $team;
    }

    public function migrateFrom2013($adaptate = FALSE)
    {
        foreach (array('Mladsi', 'Starsi') as $cat) {
            if ($adaptate) {
                $this->adaptateDatabase($cat);
            }
            foreach (array(1, 2, 3) as $day) {
                $this->insertToDatabase($cat, $this->decodeHtmlTable($cat, $day));
            }
        }
    }

    private function adaptateDatabase($cat)
    {
        $matchTable = $this->matchTablePrefix13 . $cat;
        $teamTable = $this->teamTablePrefix13 . $cat;

        $this->context->query("ALTER TABLE $teamTable ENGINE='InnoDB';>");
        $this->context->query("ALTER TABLE $matchTable ENGINE='InnoDB';");
        $this->context->query("ALTER TABLE $teamTable CHANGE `id_teamu` `id_teamu` int(11) NOT NULL AUTO_INCREMENT FIRST;");
        $this->context->query("ALTER TABLE $matchTable ADD INDEX `ID_domaci` (`ID_domaci`), ADD INDEX `ID_hoste` (`ID_hoste`);");
        $this->context->query("ALTER TABLE $teamTable ADD FOREIGN KEY (`id_teamu`) REFERENCES `2013_zapasy_mladsi` (`ID_domaci`)");
        $this->context->query("ALTER TABLE $teamTable ADD FOREIGN KEY (`id_teamu`) REFERENCES `2013_zapasy_mladsi` (`ID_hoste`)");
        $this->context->query("ALTER TABLE $matchTable ADD `cas_vlozeni` datetime NULL AFTER `posledni_update`, ADD `odehrano` tinyint NOT NULL AFTER `cas_vlozeni`, ADD `cas_odehrani` datetime NOT NULL AFTER `odehrano`;");
    }

    private function insertToDatabase($cat, $dataArray)
    {
        $matchTable = $this->matchTablePrefix13 . $cat;
        $teamTable = $this->teamTablePrefix13 . $cat;
        $teams = $this->context->table($teamTable)->select('jmeno, id_teamu')->fetchPairs('jmeno', 'id_teamu');
        $teams['TJ Rožnov p.Radh.'] = $teams['TJ Rožnov p. Radhoštìm'];
        foreach ($dataArray as $match) {
            $this->context->table($matchTable)->where('ID_domaci = ? AND ID_hoste = ?', $teams[$match->homeTeam], $teams[$match->awayTeam])->update(array('cas_odehrani' => $match->time, 'cas_vlozeni' => new \DibiDateTime()));
        }
    }

    private function decodeHtmlTable($cat, $day)
    {
        $date = array(
            1 => '2013-5-24',
            2 => '2013-5-25',
            3 => '2013-5-26'
        );
        $result = new \SimpleXMLElement((file_get_contents(__DIR__ . '\..\..\misc\2013\Mini' . $cat . $day . 'DEN.php')));
        dump($result);
        $time = NULL;
        foreach ($result->tr as $match) {
            $time = ((string)$match->td[0] == "") ? $time : new \DibiDateTime($date[$day] . ' ' . (string)$match->td[0]);
            if ($time) {
                $tableOfMatches[] = ArrayHash::from(array(
                    'time' => $time,
                    'homeTeam' => (string)$match->td[1],
                    'awayTeam' => (string)$match->td[2]
                ));
            }
        }
        dump($tableOfMatches);
        return $tableOfMatches;
    }

}