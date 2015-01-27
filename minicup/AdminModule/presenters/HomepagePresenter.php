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
    /** @var ReorderManager @inject */
    public $reorder;
    /** @var MatchRepository @inject */
    private $MR;
    /** @var TeamRepository @inject */
    private $TR;

    public function renderDefault()
    {
    }

    public function actionReorder()
    {
        $cat = $this->CR->getBySlug('mladsi');
        $this->reorder->reorder($cat);
    }

}
