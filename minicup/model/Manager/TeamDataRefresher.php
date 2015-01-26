<?php

namespace Minicup\Model\Manager;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;
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
        /** @var Team[] $teams */
        $teams = array();
        /** @var Team $team */
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
                $home->points += ReorderManager::POINTS_FOR_WINNER;
                $away->points += ReorderManager::POINTS_FOR_LOSER;
            } elseif($match->scoreHome < $match->scoreAway) {
                $home->points += ReorderManager::POINTS_FOR_LOSER;
                $away->points += ReorderManager::POINTS_FOR_WINNER;
            } else {
                $home->points += ReorderManager::POINTS_FOR_DRAW;
                $away->points += ReorderManager::POINTS_FOR_DRAW;
            }
        }

        foreach ($teams as $team) {
            $this->TR->persist($team);
        }
    }
}