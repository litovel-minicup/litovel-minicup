<?php

namespace Minicup\FrontModule\Presenters;


use Minicup\Components\CategoryDetailComponent;
use Minicup\Components\ICategoryDetailComponentFactory;
use Minicup\Model\Entity\Category;

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

    public function renderDefault(Category $category)
    {
        $this->template->category = $category;
    }
}