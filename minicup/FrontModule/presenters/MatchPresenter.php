<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\ICategoryOnlineComponentFactory;
use Minicup\Components\IListOfMatchesComponentFactory;
use Minicup\Components\IMatchDetailComponentFactory;
use Minicup\Components\ListOfMatchesComponent;
use Minicup\Model\Entity\Match;

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

    /**
     * @return ListOfMatchesComponent
     */
    public function createComponentListOfMatchesComponent()
    {
        return $this->LOMCFactory->create($this->getParameter('category'));
    }

    public function createComponentMatchDetailComponent()
    {
        return $this->MDCFactory->create($this->getParameter('match'));
    }

    public function createComponentCategoryOnlineComponent()
    {
        return $this->COCFactory->create($this->category);
    }

    public function renderDetail(Match $match)
    {
        $this->template->match = $match;
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function actionOnline()
    {
        if ($this->MR->isFinished($this->category)) {
            $this->error();
        }
    }
}
