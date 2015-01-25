<?php

namespace Minicup\Model\Manager;

use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\TeamRepository;
use Nette\Object;


class TeamDataRefresher extends Object
{

    /** @var MatchRepository */
    private $MR;

    /** @var TeamRepository */
    private $TR;

    /**
     * @param MatchRepository $MR
     * @param TeamRepository $TR
     */
    public function __construct(MatchRepository $MR, TeamRepository $TR)
    {
        $this->MR = $MR;
        $this->TR = $TR;
    }

    /**
     * @param Category $category
     */
    public function refreshData(Category $category)
    {
        $teams = array();
        foreach ($this->TR->findAll() as $team) {
            $team->points = 0;
            $team->scored = 0;
            $team->received = 0;
            $teams[$team->id] = $team;
        }

        foreach ($this->MR->findMatchesByCategory($category) as $match) {
            $home = $teams[$match->homeTeam->team->id];
            $away = $teams[$match->awayTeam->team->id];

            $home->scored += $match->scoreHome;
            $away->scored += $match->scoreAway;

            $home->received += $match->scoreAway;
            $away->received += $match->scoreHome;

            if ($match->scoreHome > $match->scoreAway) {
                $home->points += 2; //TODO: set as constant
            } elseif($match->scoreHome < $match->scoreAway) {
                $away->points += 2;
            } else {
                $home->points += 1;
                $away->points += 1;
            }
        }

        foreach ($teams as $team) {
            $this->TR->persist($team);
        }
    }
}