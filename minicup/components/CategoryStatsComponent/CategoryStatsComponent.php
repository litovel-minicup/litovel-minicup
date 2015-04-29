<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;
use Minicup\Model\Manager\StatsManager;
use Nette\Utils\ArrayHash;

class CategoryStatsComponent extends BaseComponent
{
    /** @var Category */
    private $category;

    /** @var StatsManager */
    private $SM;

    public function __construct(Category $category, StatsManager $SM)
    {
        $this->category = $category;
        $this->SM = $SM;
    }

    public function render()
    {
        $this->template->stats = ArrayHash::from($this->SM->getStats($this->category));
        parent::render();
    }
}

interface ICategoryStatsComponentFactory
{
    /**
     * @param Category $category
     * @return CategoryStatsComponent
     */
    public function create(Category $category);
}