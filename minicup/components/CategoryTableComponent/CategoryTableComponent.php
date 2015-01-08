<?php

namespace Minicup\Components;

use Minicup\Model\Repository\TeamRepository;

// TODO: differences between this and ListOfTeams?
class CategoryTableComponent extends BaseComponent
{
    /**
     *
     * @var TeamRepository
     */
    private $TR;

    /**
     * @param TeamRepository $TR
     */
    public function __construct(TeamRepository $TR)
    {
        parent::__construct();
        $this->TR = $TR;
    }

    public function render()
    {
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

    public function handleRefresh()
    {
        $this->redrawControl();
    }
}
