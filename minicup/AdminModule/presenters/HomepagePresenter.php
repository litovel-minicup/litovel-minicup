<?php

namespace Minicup\AdminModule\Presenters;

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


    public function __construct(MatchRepository $MR, TeamRepository $TR)
    {
        parent::__construct();
        $this->MR = $MR;
        $this->TR = $TR;
    }

    public function renderDefault()
    {
        $this->template->teams = $this->TR->findAll();
    }

}
