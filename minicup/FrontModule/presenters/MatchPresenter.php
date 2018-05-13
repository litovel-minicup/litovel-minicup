<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\ICategoryOnlineComponentFactory;
use Minicup\Components\IListOfMatchesComponentFactory;
use Minicup\Components\IMatchDetailComponentFactory;
use Minicup\Components\ListOfMatchesComponent;
use Minicup\Model\Repository\MatchRepository;

/**
 * Match presenter.
 */
final class MatchPresenter extends BaseFrontPresenter
{
    /** @var IListOfMatchesComponentFactory @inject */
    public $LOMCFactory;

    /** @var IMatchDetailComponentFactory @inject */
    public $MDCFactory;

    /** @var ICategoryOnlineComponentFactory @inject */
    public $COCFactory;

    /** @var MatchRepository @inject */
    public $MR;

    /**
     * @return ListOfMatchesComponent
     */
    public function createComponentListOfMatchesComponent()
    {
        return $this->LOMCFactory->create($this->getParameter('category'));
    }

    public function createComponentMatchDetailComponent()
    {
        return $this->MDCFactory->create($this->MR->get(4471));
    }

    public function createComponentCategoryOnlineComponent()
    {
        return $this->COCFactory->create($this->category);
    }

    public function renderDetail()
    {
        $this->template->match = $this->MR->get(4471);
    }
}
