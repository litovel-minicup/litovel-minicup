<?php

namespace Minicup\Model\Manager;

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
use Nette\Utils\DateTime;
use Nette\Utils\Strings;

/**
 * provide migration from 2014 minicup db to 2015 db
 * @package Minicup\Model\Manager
 */
class MigrationsManager extends Object
{
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
    private $con;

    /** @var TeamReplicator  */
    private $replicator;

    /** @var  TeamDataRefresher */
    private $TDR;

    /**
     * @param CategoryRepository $CR
     * @param TeamRepository $TR
     * @param TeamInfoRepository $TIR
     * @param MatchRepository $MR
     * @param MatchTermRepository $MTR
     * @param DayRepository $DR
     * @param YearRepository $YR
     * @param Context $con
     * @param TeamReplicator $replicator
     * @param TeamDataRefresher $TDR
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
                                TeamDataRefresher $TDR)
    {
        $this->CR = $CR;
        $this->TR = $TR;
        $this->TIR = $TIR;
        $this->MR = $MR;
        $this->MTR = $MTR;
        $this->DR = $DR;
        $this->YR = $YR;
        $this->con = $con;
        $this->replicator = $replicator;
        $this->TDR = $TDR;
    }


    /**
     * @param Category $category
     * @param bool $truncate
     * @param bool $withScore
     * @param int $limit
     */
    public function migrateMatches(Category $category, $truncate = FALSE, $withScore = FALSE, $limit = NULL)
    {
        if ($truncate) {
            $this->truncate($category);
        }
        $year = $this->YR->getActualYear();
        $data = $this->con->table($this->matchTablePrefix . $category->slug)->order('cas_odehrani ASC')->limit($limit);
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
            if ($withScore) {
                $match->scoreHome = $row->SCR_domaci;
                $match->scoreAway = $row->SCR_hoste;
                $match->confirmed = 1;
            }
            $datetime = new \DibiDateTime($play_time->getTimestamp());

            $matchTerm = $this->MTR->getByStart($datetime);
            if ($matchTerm) {
                $match->matchTerm = $matchTerm;
                $this->MR->persist($match);
            }
            $day = $this->DR->getByDatetime($datetime);
            if (!$day) {
                $day = new Day();
                $day->year = $year;
                $date = clone $datetime;
                $day->day = $date->setTime(0, 0, 0);
                $this->DR->persist($day);
            }
            $matchTerm = new MatchTerm();
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
            $this->replicator->replicate($category, $match);
            $this->TDR->refreshData($category);
        }

    }

    /**
     * @param Category $category
     * @param string $name
     * @param string $slug
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
}