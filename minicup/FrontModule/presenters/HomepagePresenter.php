<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IOnlineReportComponentFactory;
use Minicup\Model\Repository\MatchRepository;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BaseFrontPresenter
{

    /** @var \Minicup\Components\IOnlineReportComponentFactory */
    private $ORCFactory;

    /** @var \Minicup\Model\Repository\MatchRepository */
    private $MR;

    public function __construct(IOnlineReportComponentFactory $ORCFactory,
                                MatchRepository $MR)
    {
        parent::__construct();
        $this->ORCFactory = $ORCFactory;
        $this->MR = $MR;
    }

    public function renderDefault()
    {
    }

    public function actionDefault()
    {
        $match = $this->MR->find(4);
        $this['onlineReportComponent']->match = $match;
    }

    public function createComponentOnlineReportComponent()
    {
        return $this->ORCFactory->create();
    }

}
