<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Model\Repository\MatchRepository;
        

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {
    /**
     * @var MatchRepository
     */
    private $MR;

    public function __construct(MatchRepository $MR) {
        parent::__construct();
        $this->MR = $MR;
    }
    public function renderDefault() {
        $this->template->matches = $this->MR->findAll();
    }
}
