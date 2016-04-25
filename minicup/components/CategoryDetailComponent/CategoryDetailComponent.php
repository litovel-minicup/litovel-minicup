<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;
use Minicup\Model\Manager\MatchManager;

interface ICategoryDetailComponentFactory
{
    /**
     * @param Category $category
     * @return CategoryDetailComponent
     */
    public function create(Category $category);
}

class CategoryDetailComponent extends BaseComponent
{
    /** @var Category */
    private $category;

    /** @var ICategoryTableComponentFactory */
    private $CTCF;

    /** @var ICategoryStatsComponentFactory */
    private $CSCF;

    /** @var ICategoryHistoryComponentFactory */
    private $CHCF;

    /** @var MatchManager */
    private $MM;

    /**
     * @param Category                         $category
     * @param ICategoryTableComponentFactory   $CTCF
     * @param ICategoryStatsComponentFactory   $CSCF
     * @param ICategoryHistoryComponentFactory $CHCF
     * @param MatchManager                     $MM
     */
    public function __construct(Category $category,
                                ICategoryTableComponentFactory $CTCF,
                                ICategoryStatsComponentFactory $CSCF,
                                ICategoryHistoryComponentFactory $CHCF,
                                MatchManager $MM)
    {
        parent::__construct();
        $this->category = $category;
        $this->CTCF = $CTCF;
        $this->CSCF = $CSCF;
        $this->CHCF = $CHCF;
        $this->MM = $MM;
    }

    public function render()
    {
        $this->template->category = $this->category;
        parent::render();
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->MM->isStarted($this->category);
    }

    public function createComponentCategoryTableComponent()
    {
        return $this->CTCF->create($this->category);
    }

    public function createComponentCategoryStatsComponent()
    {
        return $this->CSCF->create($this->category);
    }

    public function createComponentCategoryHistoryComponent()
    {
        return $this->CHCF->create($this->category);
    }
}