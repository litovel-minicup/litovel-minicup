<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IListOfMatchesComponentFactory;
use Minicup\Components\ListOfMatchesComponent;

/**
 * Match presenter.
 */
final class MatchPresenter extends BaseFrontPresenter
{
    /** @var IListOfMatchesComponentFactory @inject */
    public $LOMCFactory;

    /**
     * @return ListOfMatchesComponent
     */
    public function createComponentListOfMatchesComponent()
    {
        return $this->LOMCFactory->create($this->getParameter('category'));
    }
}
