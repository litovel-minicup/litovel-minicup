<?php

namespace Minicup\FrontModule\Presenters;


use Minicup\Components\ICategoryTableComponentFactory;
use Minicup\Model\Entity\Category;
use Nette\Application\UI\Multiplier;

class ResultPresenter extends BaseFrontPresenter
{
    /** @var ICategoryTableComponentFactory @inject */
    public $CCF;

    public function renderTable(Category $category)
    {

    }

    public function createComponentCategoryTableComponent()
    {
        $CR = $this->CR;
        $me = $this;
        return new Multiplier(function ($categorySlug) use ($CR, $me) {
            $category = $CR->getBySlug($categorySlug);
            return $me->CCF->create($category);
        });
    }
}