<?php

namespace Minicup\FrontModule\Presenters;


use Minicup\Components\CategoryDetailComponent;
use Minicup\Components\ICategoryDetailComponentFactory;

class StatsPresenter extends BaseFrontPresenter
{
    /** @var ICategoryDetailComponentFactory @inject */
    public $CDCF;

    /**
     * @return CategoryDetailComponent
     */
    protected function createComponentCategoryDetailComponent()
    {
        return $this->CDCF->create($this->getParameter('category'));
    }
}