<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;
use Minicup\Model\Manager\MatchManager;
use Minicup\Model\Manager\StatsManager;
use Nette\Utils\ArrayHash;

interface ICategoryStatsComponentFactory
{
    /**
     * @param Category $category
     * @return CategoryStatsComponent
     */
    public function create(Category $category);
}

class CategoryStatsComponent extends BaseComponent
{
    /** @var Category */
    private $category;

    /** @var StatsManager */
    private $SM;

    /** @var MatchManager */
    private $MM;

    /**
     * @param Category     $category
     * @param StatsManager $SM
     * @param MatchManager $MM
     */
    public function __construct(Category $category,
                                StatsManager $SM,
                                MatchManager $MM)
    {
        parent::__construct();
        $this->category = $category;
        $this->SM = $SM;
        $this->MM = $MM;
    }

    public function render()
    {
        $this->template->stats = ArrayHash::from($this->SM->getStats($this->category));
        parent::render();
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->MM->isStarted($this->category);
    }
}