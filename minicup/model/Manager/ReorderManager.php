<?php

namespace Minicup\Model\Manager;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Minicup\Model\Repository\TeamRepository;
use Nette\Object;


/**
 * Class for conversion order
 * @package Minicup\Model\Manager
 */
class ReorderManager extends Object
{
    /** @var  TeamRepository */
    private $TR;

    /** @var TeamInfoRepository */
    private $TIR;

    /** @var  MatchRepository */
    private $MR;

    private $teams;

    /** @var  Team[] */
    private $teamsEntities;

    private $teamPointsFromPoints;

    public function __construct(TeamRepository $TR, TeamInfoRepository $TIR, MatchRepository $MR)
    {
        $this->TR = $TR;
        $this->TIR = $TIR;
        $this->MR = $MR;
    }

    public function reorder(Category $category)
    {
        foreach ($category->teams as $team) {
            $this->teamsEntities[$team->id] = $team;
        }
        $this->teams = $category->teams;
        $this->orderByPoints();


        dump($this->teamPointsFromPoints);
        foreach ($this->teams as $team) {
            dump($team->order);
        }
    }


    private function orderByPoints()
    {
        $this->teamPointsFromPoints = [];
        foreach ($this->teams as $reorderingTeam) {
            $teamPoints = 0;
            foreach ($this->teams as $comparativeTeam) {
                if ($reorderingTeam->points > $comparativeTeam->points) {
                    $teamPoints += 1;
                }
            }
            $this->teamPointsFromPoints[$reorderingTeam->id] = $teamPoints;
        }
        //Vytvoří pole, kde klíč == počet bodů zjískaných z předchozího porovnání a hodnota == počet týmů se stejným počtem bodů
        $pointScale = array_count_values($this->teamPointsFromPoints);
        $teamPosition = count($this->teams);

        //Každému týmu přiřadí umístění, pokud je stejný počet bodů -> týmy se pošlou na seřazení podle vzájemných zápasů
        foreach ($pointScale as $key => $countOfTeamsWithSamePoints) {
            if ($countOfTeamsWithSamePoints == 1) {
                $teamID = array_search($key, $this->teamPointsFromPoints);
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

    private function orderByMutualMatch($points, $countOfTeamsWithSamePoints, $teamWorsePosition) //$countOfTeamsWithSamePoints -- asi nepotřebuji
    {
        $teamsToCompare = [];
        foreach ($this->teamPointsFromPoints as $key => $foo) {
            if ($foo == $points) {
                $teamsToCompare[] = $this->teamsEntities[$key];
            }
        }

        if ($countOfTeamsWithSamePoints == 2) {
            $commonMatch = $this->MR->getCommonMatchForTeams($teamsToCompare[0], $teamsToCompare[1]);
            if ($commonMatch != NULL AND $commonMatch->scoreHome = !$commonMatch->scoreAway) {
                if ($teamsToCompare[0]->i->id == $commonMatch->homeTeam XOR $commonMatch->scoreHome > $commonMatch->scoreAway) {
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
            } else if ($commonMatch->scoreHome == $commonMatch->scoreAway) {
                //order by next rules
            } else {
                //order by next rules
            }
        } else {
            //miniTableWithMutalMatch
        }
    }

}