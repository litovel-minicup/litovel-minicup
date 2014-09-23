<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Model\Repository\MatchRepository,
        \Minicup\Model\Repository\TeamRepository;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {

    /**
     * @var MatchRepository
     */
    private $MR;
    
    /**
     * @var TeamRepository
     */
    private $TM;

    public function __construct(MatchRepository $MR, TeamRepository $TM) {
        parent::__construct();
        $this->MR = $MR;
        $this->TM = $TM;
    }

    public function renderDefault() {
        $this->template->teams = $this->TM->findAll();
    }

}
