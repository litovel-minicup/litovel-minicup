<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IListOfMatchesComponentFactory;
use Minicup\Model\Entity\Category;

/**
 * Team presenter.
 */
final class MatchPresenter extends BaseFrontPresenter
{

    /** @var IListOfMatchesComponentFactory @inject */
    public $LOMCFactory;

    public function renderDefault(Category $category = NULL)
    {
        if ($category) {
            $this->template->category = $category;
        } else {
            $this->template->categories = $this->CR->findAll();
        }
    }

    public function createComponentListOfMatchesComponent()
    {
        return $this->LOMCFactory->create($this->getParameter('category'));

    }
}
