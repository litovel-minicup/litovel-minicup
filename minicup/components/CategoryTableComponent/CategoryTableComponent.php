<?php

namespace Minicup\Components;

use \Nette\Application\UI\Control;

/**
 * 
 */
class CategoryTableComponent extends Control {
    /**
     *
     * @var \Minicup\Model\Repository\TeamRepository
     */
    private $TR;
    
    public function __construct(\Minicup\Model\Repository\TeamRepository $TR) {
        parent::__construct();
        $this->TR = $TR;
    }
    public function render() {
        $this->template->setFile(__DIR__ . '/CategoryTableComponent.latte');
        $this->template->teams = $this->TR->findAll();
        $this->template->time = time();
        if ($this->getPresenter()->isAjax()) {
            $this->template->state = 'ajax';
        } else {
            $this->template->state = 'render';
        }
        $this->template->render();
    }
    public function handleRefresh() {
        $this->redrawControl();
    }
}
