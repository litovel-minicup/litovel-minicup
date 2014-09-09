<?php

namespace Minicup\AdminModule\Presenters;

use Nette,
    Minicup\Model\Entity;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {
    /**
     * @var \Minicup\Model\Repository\MatchRepository
     */
    private $MR;

    public function __construct(\Minicup\Model\Repository\MatchRepository $MR) {
        parent::__construct();
        $this->MR = $MR;
    }
    public function renderDefault() {
        $this->template->matches = $this->MR->findAll();
    }
}
