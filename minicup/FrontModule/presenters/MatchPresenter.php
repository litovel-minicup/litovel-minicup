<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IListOfMatchesComponentFactory;

/**
 * Match presenter.
 */
final class MatchPresenter extends BaseFrontPresenter
{
    /** @var IListOfMatchesComponentFactory @inject */
    public $LOMCFactory;

    public function createComponentListOfMatchesComponent()
    {
        return $this->LOMCFactory->create($this->getParameter('category'));
    }
}
