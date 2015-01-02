<?php

namespace Minicup\Model\Manager;


use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\TeamRepository;
use Nette\Object;


/*
 * restructure teams from
 *
 * 1 - foo, actual=1
 * 2 - bar, actual=1
 * 3 - foo-bar, actual=1
 *
 * to
 *
 * 1 - foo, actual=0
 * 2 - bar, actual=0
 * 3 - foo-bar, actual=0
 * 4 - foo, actual=1
 * 5 - bar, actual=1
 * 6 - foo-bar, actual=1
 */

class TeamReplicator extends Object
{
    /** @var  TeamRepository */
    private $TR;

    public function __construct(TeamRepository $TR)
    {
        $this->TR = $TR;
    }

    /**
     * @param Category $category
     * @param Match $afterMatch
     */
    public function replicate(Category $category, Match $afterMatch = NULL)
    {
        foreach ($category->teams as $oldTeam) {
            $newTeam = new Team();
            $newTeam->info = $oldTeam->info;
            $newTeam->actual = 1;
            $oldTeam->actual = 0;
            $oldTeam->afterMatch = $afterMatch;
        }

    }


}