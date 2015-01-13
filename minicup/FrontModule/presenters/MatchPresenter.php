<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IListOfMatchesComponentFactory;
use Minicup\Model\Entity\Category;
use Nette\Application\UI\Multiplier;

/**
 * Match presenter.
 */
final class MatchPresenter extends BaseFrontPresenter
{

    /** @var IListOfMatchesComponentFactory @inject */
    public $LOMCFactory;

    public function renderDefault(Category $category = NULL)
    {
        if ($category) {
            $this->template->category = $category;
        }
    }

    public function createComponentListOfMatchesComponent()
    {
        $CR = $this->CR;
        return new Multiplier(function ($categorySlug) use ($CR) {
            $category = $CR->getBySlug($categorySlug);
            return $this->LOMCFactory->create($category);
        });
    }
}
