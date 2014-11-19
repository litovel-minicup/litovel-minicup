<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IListOfMatchesComponentFactory;
use Minicup\Components\IListOfTeamsComponentFactory;
use Minicup\Components\IOnlineReportComponentFactory;
use Minicup\Model\Repository\MatchRepository;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BaseFrontPresenter
{
    /** @var \Minicup\Model\Repository\MatchRepository */
    private $MR;

    /** @var \Minicup\Components\IOnlineReportComponentFactory */
    private $ORCFactory;

    /** @var IListOfMatchesComponentFactory */
    private $LOFCFactory;

    /** @var  IListOfTeamsComponentFactory @inject */
    public $LOTCFactory;

    /**
     * @param IOnlineReportComponentFactory $ORCFactory
     * @param IListOfMatchesComponentFactory $LOFCFactory
     * @param MatchRepository $MR
     */
    public function __construct(IOnlineReportComponentFactory $ORCFactory,
                                IListOfMatchesComponentFactory $LOFCFactory,
                                MatchRepository $MR)
    {
        parent::__construct();
        $this->ORCFactory = $ORCFactory;
        $this->LOFCFactory = $LOFCFactory;
        $this->MR = $MR;
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

}
