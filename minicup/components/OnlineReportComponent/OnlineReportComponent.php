<?php

namespace Minicup\Components;

use \Nette\Application\UI\Control,
    \Minicup\Model\Repository\OnlineReportRepository;

/**
 * 
 */
class OnlineReportComponent extends Control {

    /** @var \Minicup\Model\Repository\OnlineReportRepository */
    private $ORR;

    public function __construct(OnlineReportRepository $ORR) {
        parent::__construct();
        $this->ORR = $ORR;
    }

    public function render(\Minicup\Model\Entity\Match $match) {
        $this->template->setFile(__DIR__ . '/OnlineReportComponent.latte');
        $this->template->match = $match;
        $this->template->render();
    }

    public function handleRefresh() {
        $this->invalidateControl();
    }

}
