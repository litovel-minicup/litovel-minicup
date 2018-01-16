<?php

namespace Minicup\Model\Manager;


use Dibi\DriverException;
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
            $line = Strings::split($line, "#\t#");

            /** @var \DateTime $datetime */
            $datetime = \DateTime::createFromFormat('j. n. Y H:i', $line[0] . ' ' . $line[1]);

            $home = $this->getTeamInfo($category, $line[2]);
            $away = $this->getTeamInfo($category, $line[3]);
            $term = $this->getMatchTerm($datetime, $category->year);

            $match = new Match();
            $match->category = $category;
            $match->homeTeam = $home;
            $match->awayTeam = $away;
            $match->matchTerm = $term;

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

    protected function getMatchTerm(\DateTime $dt, Year $year)
    {
        $term = $this->MTR->getByStart($dt);
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
        $term->start = $dt;
        $dt->add(new \DateInterval('PT30M'));
        $term->end = $dt;

        $term->day = $day;
        $term->location = ''; // TODO: small hack, better refactor location arg
        $this->MTR->persist($term);
        return $term;

    }
}