<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Model\Manager\ReorderManager;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\TeamRepository;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BaseAdminPresenter
{
    /**
     * @var MatchRepository
     */
    private $MR;

    /**
     * @var TeamRepository
     */
    private $TR;

    /** @var  ReorderManager */
    private $reorder;

    /**
     * @param MatchRepository $MR
     * @param TeamRepository  $TR
     * @param ReorderManager  $reorder
     */
    public function __construct(MatchRepository $MR, TeamRepository $TR, ReorderManager $reorder)
    {
        parent::__construct();
        $this->MR = $MR;
        $this->TR = $TR;
        $this->reorder = $reorder;
    }

    public function renderDefault()
    {
    }

    public function actionReorder()
    {
        $cat = $this->CR->getBySlug('mladsi');
        $this->reorder->reorder($cat);
    }
}
