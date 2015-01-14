<?php

namespace Minicup\Model\Manager;

use Minicup\Model\Entity\Category;
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

    public function __construct(TeamRepository $TR)
    {
        $this->TR = $TR;
    }

    public function reorder(Category $category)
    {
        // some magic with $category
    }

}