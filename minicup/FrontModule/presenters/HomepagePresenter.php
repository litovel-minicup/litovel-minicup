<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IListOfMatchesComponentFactory;
use Minicup\Components\IListOfTeamsComponentFactory;
use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\YearRepository;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseFrontPresenter
{

    /** @var IListOfTeamsComponentFactory @inject */
    public $LOTCFactory;

    /** @var \Minicup\Components\ITeamDetailComponentFactory @inject */
    public $TDCFactory;

    /** @var MatchRepository @inject */
    public $MR;

    /** @var \Minicup\Model\Repository\TeamRepository @inject */
    public $TR;

    /** @var \Minicup\Components\IOnlineReportComponentFactory @inject */
    public $ORCFactory;

    /** @var IListOfMatchesComponentFactory @inject */
    public $LOFCFactory;

    /** @var YearRepository @inject */
    public $YR;


    public function renderMatches()
    {
        $this->template->years = $this->YR->findAll();
    }

    public function renderCategory(Category $category)
    {
        $this->template->category = $category;
    }

    public function createComponentOnlineReportComponent()
    {
        $match = $this->MR->find(4);
        return $this->ORCFactory->create($match);
    }


    public function createComponentListOfMatchesComponent()
    {
        return $this->LOFCFactory->create();
    }

    public function createComponentListOfTeamsComponent()
    {
        return $this->LOTCFactory->create();
    }


    public function createComponentTeamDetailComponent()
    {
        $team = $this->TR->find(1);
        return $this->TDCFactory->create($team);
    }
}
