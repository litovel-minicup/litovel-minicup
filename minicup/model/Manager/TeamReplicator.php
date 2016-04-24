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
    public function __construct(TeamRepository $TR, CategoryRepository $CR)
    {
        $this->TR = $TR;
        $this->CR = $CR;
    }

    /**
     * @param Category $category
     * @param Match    $afterMatch
     */
    public function replicate(Category $category, Match $afterMatch = NULL)
    {
        foreach ($this->TR->getByCategory($category) as $oldTeam) {
            $newTeam = new Team();
            $data = $oldTeam->getData(array('i', 'order', 'points', 'scored', 'received', 'category'));
            $newTeam->i = $oldTeam->i;
            $newTeam->actual = 1;
            $oldTeam->actual = 0;
            $newTeam->assign($data);
            $oldTeam->afterMatch = $afterMatch;
            $this->TR->persist($oldTeam);
            $this->TR->persist($newTeam);
        }

    }


}