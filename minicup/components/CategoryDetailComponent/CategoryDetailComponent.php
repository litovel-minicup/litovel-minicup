<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;

class CategoryDetailComponent extends BaseComponent
{
    /** @var Category */
    private $category;

    /** @var ICategoryTableComponentFactory @inject */
    private $CTCF;

    /** @var ICategoryStatsComponentFactory @inject */
    private $CSCF;

    /** @var ICategoryHistoryComponentFactory @inject */
    private $CHCF;

    /**
     * @param Category $category
     * @param CategoryTableComponent $CTCF
     * @param CategoryStatsComponent $CSCF
     * @param CategoryHistoryComponent $CHCF
     */
    public function __construct(Category $category,
                                ICategoryTableComponentFactory $CTCF,
                                ICategoryStatsComponentFactory $CSCF,
                                ICategoryHistoryComponentFactory $CHCF)
    {
        $this->category = $category;
        $this->CTCF = $CTCF;
        $this->CSCF = $CSCF;
        $this->CHCF = $CHCF;
    }

    public function render()
    {
        $this->template->category = $this->category;
        parent::render();
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

interface ICategoryDetailComponentFactory
{
    /**
     * @param Category $category
     * @return CategoryDetailComponent
     */
    public function create(Category $category);
}