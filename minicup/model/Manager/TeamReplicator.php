<?php

namespace Minicup\Model\Manager;


use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\TeamRepository;
use Nette\Object;

/**
 * Class for creating team history - it's clone actual teams to historically context
 * @package Minicup\Model\Manager
 */
class TeamReplicator extends Object
{
    /** @var  TeamRepository */
    private $TR;

    /** @var  CategoryRepository */
    private $CR;

    /**
     * @param TeamRepository     $TR
     * @param CategoryRepository $CR
     */
    public function __construct(TeamRepository $TR,
                                CategoryRepository $CR)
    {
        $this->TR = $TR;
        $this->CR = $CR;
    }

    /**
     * @param Category $category
     * @param Match    $afterMatch
     */
    public function replicate(Category $category, Match $afterMatch)
    {
        foreach ($category->teams as $oldTeam) {
            $newTeam = new Team();
            $newTeam->i = $oldTeam->i;
            $newTeam->category = $oldTeam->category;
            $newTeam->received = $newTeam->scored = $newTeam->order = $newTeam->points = 0;
            $newTeam->actual = 1;
            $newTeam->afterMatch = $afterMatch;

            $oldTeam->actual = 0;
            $this->TR->persist($oldTeam);

            $this->TR->persist($newTeam);
        }

    }


}