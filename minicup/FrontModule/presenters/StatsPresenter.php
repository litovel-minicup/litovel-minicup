<?php

namespace Minicup\FrontModule\Presenters;


use Minicup\Components\ICategoryStatsComponentFactory;
use Minicup\Components\ICategoryTableComponentFactory;
use Minicup\Model\Entity\Category;

class StatsPresenter extends BaseFrontPresenter
{
    /** @var ICategoryTableComponentFactory @inject */
    public $CCF;

    /** @var ICategoryStatsComponentFactory @inject */
    public $CSCF;

    public function renderDefault(Category $category)
    {

    }

    public function createComponentCategoryTableComponent()
    {
        return $this->CCF->create($this->getParameter('category'));
    }

    public function createComponentCategoryStatsComponent()
    {
        return $this->CSCF->create($this->getParameter('category'));
    }
}