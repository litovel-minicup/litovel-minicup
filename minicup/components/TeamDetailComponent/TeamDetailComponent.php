<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\TeamRepository;
use Nette\Application\UI\Control;

class TeamDetailComponent extends Control
{

    /** @var Team */
    public $team;

    /** @var \Minicup\Model\Repository\TeamRepository */
    private $TR;
    /** @var  IListOfMatchesComponentFactory */
    private $LOMCF;

    public function __construct(Team $team, TeamRepository $TR, IListOfMatchesComponentFactory $LOMCF)
    {
        parent::__construct();
        $this->team = $team;
        $this->TR = $TR;
        $this->LOMCF = $LOMCF;
    }

    public function render()
    {
        $this->template->setFile(__DIR__ . '/TeamDetailComponent.latte');
        $this->template->team = $this->team;
        $this->template->render();
    }

    public function createComponentListOfMatchesComponent()
    {
        return $this->LOMCF->create($this->team);
    }

}