<?php

namespace Minicup\Model\Manager;

use Minicup\Model\Entity\Category;
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

    public function __construct(TeamRepository $TR, TeamInfoRepository $TIR)
    {
        $this->TR = $TR;
        $this->TIR = $TIR;
    }

    public function reorder(Category $category)
    {
        $this->orderByPoints($category);
    }


    private function orderByPoints(Category $category)
    {

        $teamPointsFromPoints = [];
        foreach ($category->teams as $reorderingTeamID) {
            $teamPoints = 0;
            foreach ($category->teams as $comparativeTeamID) {
                if ($reorderingTeamID->points > $comparativeTeamID->points) {
                    $teamPoints += 1;
                }
            }
            $teamPointsFromPoints[$reorderingTeamID->id] = $teamPoints;
        }

        $pointScale = array_count_values($teamPointsFromPoints);
        $teamPosition = count($category->teams);

        foreach ($pointScale as $key => $countOfTeamsWithSamePoints) {
            if ($countOfTeamsWithSamePoints == 1) {
                $teamID = array_search($key, $teamPointsFromPoints);
                foreach ($category->teams as $team) {
                    if ($team->id == $teamID) {
                        $team->order = $teamPosition;
                    }
                }
            } else {
                $this->orderByMutualMatch($category, $teamPosition);
            }
            $teamPosition -= $countOfTeamsWithSamePoints;
        }
    }

    private function orderByMutualMatch(Category $category, $teamWorsePosition)
    {

    }

}