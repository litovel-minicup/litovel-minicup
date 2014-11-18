<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IListOfMatchesComponentFactory;
use Minicup\Components\IListOfTeamsComponentFactory;
use Minicup\Components\IOnlineReportComponentFactory;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\TeamRepository;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseFrontPresenter
{

    /** @var IListOfTeamsComponentFactory @inject */
    public $LOTCFactory;

    /** @var \Minicup\Components\ITeamDetailComponentFactory @inject */
    public $TDCFactory;

    /** @var MatchRepository */
    private $MR;

    /** @var \Minicup\Model\Repository\TeamRepository */
    private $TR;

    /** @var \Minicup\Components\IOnlineReportComponentFactory */
    private $ORCFactory;

    /** @var IListOfMatchesComponentFactory */
    private $LOFCFactory;

    /**
     * @param IOnlineReportComponentFactory  $ORCFactory
     * @param IListOfMatchesComponentFactory $LOFCFactory
     * @param MatchRepository                $MR
     * @param TeamRepository                 $TR
     */
    public function __construct(IOnlineReportComponentFactory $ORCFactory,
                                IListOfMatchesComponentFactory $LOFCFactory,
                                MatchRepository $MR,
                                TeamRepository $TR)
    {
        parent::__construct();
        $this->ORCFactory = $ORCFactory;
        $this->LOFCFactory = $LOFCFactory;
        $this->MR = $MR;
        $this->TR = $TR;
    }

    public function renderDefault()
    {
    }

    public function actionDefault()
    {
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
