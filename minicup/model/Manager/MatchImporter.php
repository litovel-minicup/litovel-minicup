<?php

namespace Minicup\Model\Manager;


use Dibi\DriverException;
use LeanMapper\Exception\InvalidValueException;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Day;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\MatchTerm;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Entity\Year;
use Minicup\Model\Repository\DayRepository;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\MatchTermRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Minicup\Model\Repository\TeamRepository;
use Nette\UnexpectedValueException;
use Nette\Utils\Strings;

class MatchImporter
{
    /** @var MatchRepository */
    private $MR;

    /** @var MatchTermRepository */
    private $MTR;

    /** @var DayRepository */
    private $DR;

    /** @var TeamInfoRepository */
    private $TIR;

    /** @var TeamRepository */
    private $TR;

    /**
     * @param MatchRepository     $MR
     * @param MatchTermRepository $MTR
     * @param DayRepository       $DR
     * @param TeamInfoRepository  $TIR
     * @param TeamRepository      $TR
     */
    public function __construct(MatchRepository $MR,
                                MatchTermRepository $MTR,
                                DayRepository $DR,
                                TeamInfoRepository $TIR,
                                TeamRepository $TR)
    {
        $this->MR = $MR;
        $this->MTR = $MTR;
        $this->DR = $DR;
        $this->TIR = $TIR;
        $this->TR = $TR;
    }

    /**
     * Import matches to category from given file in format:
     * j. n. Y <tab> H:i <tab> team A <tab> team B <end line>
     * @param Category $category
     * @param string   $file
     * @return int count of imported matches
     * @throws DriverException
     * @throws \Exception
     */
    public function import(Category $category, $file)
    {
        $count = 0;
        $data = file_get_contents($file);
        foreach (Strings::split($data, "#\r|\n#") as $line) {
            $line = Strings::split(Strings::trim($line), "#\t#");
            if (!$line) continue;

            // WITH LEADING ZEROS!
            /** @var \DateTime $datetime */
            $datetime = \DateTime::createFromFormat("d. m. Y H:i", $line[0] . ' ' . $line[1]);
            $location = $line[2];
            if (!$datetime) {
                $parsed = $line[0] . ' ' . $line[1];
                throw new UnexpectedValueException("Cannot parse date '{$parsed}'");
            }

            $home = $this->getTeamInfo($category, $line[3]);
            $away = $this->getTeamInfo($category, $line[4]);
            $term = $this->getMatchTerm($datetime, $category->year, $location);

            $match = new Match();
            $match->category = $category;
            $match->homeTeam = $home;
            $match->awayTeam = $away;
            $match->matchTerm = $term;
            $match->onlineState = Match::INIT_ONLINE_STATE;

            try {
                $this->MR->persist($match);
                $count++;
            } catch (DriverException $e) {
                throw $e;
            }

        }
        return $count;
    }

    protected function getTeamInfo(Category $category, $name)
    {
        $teamInfo = $this->TIR->getByName($category, $name);
        if ($teamInfo) {
            return $teamInfo;
        }
        $teamInfo = new TeamInfo();
        $teamInfo->name = $name;
        $teamInfo->slug = Strings::webalize($name);
        $teamInfo->category = $category;

        $this->TIR->persist($teamInfo);

        $team = new Team();
        $team->i = $teamInfo;
        $team->actual = 1;
        $team->points = $team->order = $team->scored = $team->received = 0;
        $team->category = $category;

        $this->TR->persist($team);
        $this->TIR->persist($teamInfo);
        return $teamInfo;
    }

    protected function getMatchTerm(\DateTime $dt, Year $year, string $location)
    {
        $term = $this->MTR->getByStart($dt, $location);
        if ($term) {
            return $term;
        }
        $day = $this->DR->getByDatetime($dt);
        if (!$day) {
            $day = new Day();
            $day->year = $year;
            $dayDt = clone $dt;
            $dayDt->setTime(0, 0);
            $day->day = $dayDt;
            $this->DR->persist($day);
        }
        $term = new MatchTerm();
        $dt->setDate(1, 1, 1);
        $term->start = clone $dt;
        $dt->add(new \DateInterval('PT30M'));
        $term->end = clone $dt;

        $term->day = $day;
        $term->location = $location;
        $this->MTR->persist($term);
        return $term;

    }
}