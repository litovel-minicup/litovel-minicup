<?php

namespace Minicup\Model\Manager;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Day;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\MatchTerm;
use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\DayRepository;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\MatchTermRepository;
use Minicup\Model\Repository\TeamRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Database\Context;
use Nette\Object;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;

class MigrationsManager extends Object
{

    /** @var  CategoryRepository */
    private $CR;

    /** @var  TeamRepository */
    private $TR;

    /** @var  MatchRepository */
    private $MR;

    /** @var  MatchTermRepository */
    private $MTR;

    /** @var DayRepository */
    private $DR;

    /** @var  YearRepository */
    private $YR;

    /** @var  Context */
    private $con;


    /**
     * @param CategoryRepository $CR
     * @param TeamRepository $TR
     * @param MatchRepository $MR
     * @param MatchTermRepository $MTR
     * @param DayRepository $DR
     * @param YearRepository $YR
     * @param Context $con
     */
    public function __construct(CategoryRepository $CR,
                                TeamRepository $TR,
                                MatchRepository $MR,
                                MatchTermRepository $MTR,
                                DayRepository $DR,
                                YearRepository $YR,
                                Context $con)
    {
        $this->CR = $CR;
        $this->TR = $TR;
        $this->MR = $MR;
        $this->MTR = $MTR;
        $this->DR = $DR;
        $this->YR = $YR;
        $this->con = $con;
    }


    /**
     * Migrate old database to new database for $category. Is NOT foolproof! And ofc. decompose this!
     * @param Category $category
     */
    public function migrate(Category $category)
    {
        $year = $this->YR->getActualYear();
        foreach ($this->con->table('2014_zapasy_' . $category->slug) as $row) {
            /** @var DateTime $play_time */
            $play_time = $row->cas_odehrani;
            $play_time->add(\DateInterval::createFromDateString('1 year'));
            $homeName = $row->ref('2014_tymy_' . $category->slug, 'ID_domaci')->jmeno;
            $awayName = $row->ref('2014_tymy_' . $category->slug, 'ID_hoste')->jmeno;
            $homeNameSlug = Strings::webalize($homeName);
            $awayNameSlug = Strings::webalize($awayName);

            $homeTeam = $this->TR->getBySlug($homeNameSlug, $category);
            $awayTeam = $this->TR->getBySlug($awayNameSlug, $category);

            if (!$homeTeam) {
                $homeTeam = new Team();
                $homeTeam->category = $category;
                $homeTeam->name = $homeName;
                $homeTeam->slug = $homeNameSlug;
                $homeTeam->order = 1;
                $this->TR->persist($homeTeam);
            }
            if (!$awayTeam) {
                $awayTeam = new Team();
                $awayTeam->category = $category;
                $awayTeam->name = $awayName;
                $awayTeam->slug = $awayNameSlug;
                $awayTeam->order = 1;
                $this->TR->persist($awayTeam);
            }
            $match = new Match();
            $match->category = $category;
            $match->homeTeam = $homeTeam;
            $match->awayTeam = $awayTeam;

            $dt = new \DibiDateTime($play_time->getTimestamp());
            $date = clone $dt;
            $time = clone $dt;
            $matchTerm = $this->MTR->getByStart($dt);
            $day = $this->DR->getByDate($date->setTime(0, 0));
            // TODO: buggy generating days!
            if (!$matchTerm->day == $day) {
                $matchTerm = new MatchTerm();
                $matchTerm->start = $time->setDate(0, 0, 0);
                $matchTerm->end = $dt->add(\DateInterval::createFromDateString('30 minute'));
                $day = $this->DR->getByDate($date->setTime(0, 0));
                if (!$day) {
                    $day = new Day();
                    $day->year = $year;
                    $day->day = $date;
                    $this->DR->persist($day);
                }
                $matchTerm->day = $day;
                $this->MTR->persist($matchTerm);
            }
            $match->matchTerm = $matchTerm;
            $this->MR->persist($match);

        }

    }
}