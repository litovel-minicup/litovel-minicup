<?php

namespace Minicup\Model\Manager;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\TeamRepository;
use Nette\Object;
use Tracy\Debugger;


/**
 * Class for conversion order
 * @package Minicup\Model\Manager
 */
class ReorderManager extends Object
{

    const POINTS_FOR_WINNER = 2;
    const POINTS_FOR_DRAW = 1;
    const POINTS_FOR_LOSER = 0;

    /** @var  TeamRepository */
    private $TR;

    /** @var  MatchRepository */
    private $MR;

    /** @var  Team[] */
    private $teams;

    /** @var  Team[] */
    private $teamsEntities;

    private $teamPointsFromPoints;


    public function __construct(TeamRepository $TR, MatchRepository $MR)
    {
        $this->TR = $TR;
        $this->MR = $MR;
    }

    /**
     * @param Category $category
     */
    public function reorder(Category $category)
    {
        foreach ($category->teams as $team) {
            $this->teamsEntities[$team->id] = $team;
        }
        $this->teams = $category->teams;
        $this->orderByPoints();

        Debugger::barDump($this->teamPointsFromPoints, 'teamPointsFromPoints');
        foreach ($this->teams as $team) {
            Debugger::barDump('i: ' . $team->i->id . ', order: ' . $team->order, $team->id);
        }
    }

    private function orderByPoints()
    {
        $this->teamPointsFromPoints = array();
        foreach ($this->teams as $reorderingTeam) {
            $teamPoints = 0;
            foreach ($this->teams as $comparativeTeam) {
                if ($reorderingTeam->points > $comparativeTeam->points) {
                    $teamPoints += 1;
                }
            }
            $this->teamPointsFromPoints[$reorderingTeam->id] = $teamPoints;
        }
        //Vytvoří pole, kde klíč == počet bodů získaných z předchozího porovnání a hodnota == počet týmů se stejným počtem bodů
        $pointScale = array_count_values($this->teamPointsFromPoints);
        $teamPosition = count($this->teams);

        //Každému týmu přiřadí umístění, pokud je stejný počet bodů -> týmy se pošlou na seřazení podle vzájemných zápasů
        $this->teamOrderByInternalPoints($pointScale, $teamPosition);
    }

    /**
     * @param array $pointScale
     * @param int $teamPosition
     * @param int|NULL $teamPoints
     */
    private function teamOrderByInternalPoints($pointScale, $teamPosition, $teamPoints = NULL)
    {
        ksort($pointScale);
        if ($teamPoints == NULL) {
            $teamPoints = $this->teamPointsFromPoints;
        }
        if (count($pointScale) == 1) {
            $teamsToCompare = array();
            foreach (array_keys($teamPoints) as $key) {
                $teamsToCompare[] = $this->teamsEntities[$key];
            }
            $this->orderByDifferenceBetweenScoredAndReceived($teamsToCompare, $pointScale[0]);
        } else {
            foreach ($pointScale as $key => $countOfTeamsWithSamePoints) {
                if ($countOfTeamsWithSamePoints == 1) {
                    $teamID = array_search($key, $teamPoints);
                    foreach ($this->teams as $team) {
                        if ($team->id == $teamID) {
                            $team->order = $teamPosition;
                        }
                    }
                } else {
                    $this->orderByMutualMatch($key, $countOfTeamsWithSamePoints, $teamPosition);
                }
                $teamPosition -= $countOfTeamsWithSamePoints;
            }
        }
    }

    /**
     * @param $teamsToCompare
     */
    private function orderByDifferenceBetweenScoredAndReceived($teamsToCompare, $countOfTeamsWithSamePoints)
    {
        dump($countOfTeamsWithSamePoints);
        if ($countOfTeamsWithSamePoints == 2) {

        }
    }

    /**
     * @param int $points
     * @param int $countOfTeamsWithSamePoints
     * @param int $teamWorsePosition
     */
    private function orderByMutualMatch($points, $countOfTeamsWithSamePoints, $teamWorsePosition)
    {
        $teamsToCompare = array();
        foreach ($this->teamPointsFromPoints as $key => $teamPoints) {
            if ($teamPoints == $points) {
                $teamsToCompare[] = $this->teamsEntities[$key];
            }
        }

        if ($countOfTeamsWithSamePoints == 2) {
            $commonMatch = $this->MR->getCommonMatchForTeams($teamsToCompare[0], $teamsToCompare[1]);
            if ($commonMatch != NULL AND $commonMatch->scoreHome != $commonMatch->scoreAway) {
                if ($teamsToCompare[0]->i->id == $commonMatch->homeTeam->id XOR $commonMatch->scoreHome > $commonMatch->scoreAway) {
                    $winnerTeamID = $teamsToCompare[1]->id;
                    $loserTeamID = $teamsToCompare[0]->id;
                } else {
                    $winnerTeamID = $teamsToCompare[0]->id;
                    $loserTeamID = $teamsToCompare[1]->id;
                }

                foreach ($this->teams as $team) {
                    if ($team->id == $winnerTeamID) {
                        $team->order = $teamWorsePosition - 1;
                    } else if ($team->id == $loserTeamID) {
                        $team->order = $teamWorsePosition;
                    }
                }
            } else if ($commonMatch == NULL) {
                $this->orderByDifferenceBetweenScoredAndReceived($teamsToCompare, $countOfTeamsWithSamePoints); //mutualMatch == FALSE
            } else {
                $this->orderByDifferenceBetweenScoredAndReceived($teamsToCompare, $countOfTeamsWithSamePoints);
            }
        } else {
            $this->miniTableWithMutualMatch($teamsToCompare, $countOfTeamsWithSamePoints, $teamWorsePosition);
        }
    }

    /**
     * @param array $teamsToCompare
     * @param int $countOfTeamsWithSamePoints
     * @param int $teamWorsePosition
     */
    private function miniTableWithMutualMatch($teamsToCompare, $countOfTeamsWithSamePoints, $teamWorsePosition)
    {
        $teamPointsFromMiniTable = array();
        foreach (array_keys($teamsToCompare) as $key) {
            $teamPointsFromMiniTable[$key] = 0;
        }
        for ($mainTeam = 0; $mainTeam < $countOfTeamsWithSamePoints; $mainTeam++) {
            for ($comparedTeam = $mainTeam + 1; $comparedTeam < $countOfTeamsWithSamePoints; $comparedTeam++) {
                $commonMatch = $this->MR->getCommonMatchForTeams($teamsToCompare[$mainTeam], $teamsToCompare[$comparedTeam]);
                if ($commonMatch != NULL && $commonMatch->scoreHome != $commonMatch->scoreAway) {
                    if ($teamsToCompare[$mainTeam]->i->id == $commonMatch->homeTeam->id XOR $commonMatch->scoreHome > $commonMatch->scoreAway) {
                        $teamPointsFromMiniTable[$comparedTeam] += static::POINTS_FOR_WINNER;
                        $teamPointsFromMiniTable[$mainTeam] += static::POINTS_FOR_LOSER;
                    } else {
                        $teamPointsFromMiniTable[$mainTeam] += static::POINTS_FOR_WINNER;
                        $teamPointsFromMiniTable[$comparedTeam] += static::POINTS_FOR_LOSER;
                    }
                } else if ($commonMatch != NULL) {
                    $teamPointsFromMiniTable[$mainTeam] *= $this::POINTS_FOR_DRAW;
                    $teamPointsFromMiniTable[$comparedTeam] += $this::POINTS_FOR_DRAW;
                }
            }
        }
        foreach ($teamsToCompare as $key => $team) {
            $teamPointsFromMiniTable[$team->id] = $teamPointsFromMiniTable[$key];
            unset ($teamPointsFromMiniTable[$key]);
        }

        $pointScale = array_count_values($teamPointsFromMiniTable);
        $this->teamOrderByInternalPoints($pointScale, $teamWorsePosition, $teamPointsFromMiniTable);
    }

}