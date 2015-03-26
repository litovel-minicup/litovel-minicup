<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\TeamRepository;

class TeamDetailComponent extends BaseComponent
{
    /** @var Team */
    public $team;

    /** @var TeamRepository */
    private $TR;

    /** @var IListOfMatchesComponentFactory */
    private $LOMCF;

    /**
     * @param Team $team
     * @param TeamRepository $TR
     * @param IListOfMatchesComponentFactory $LOMCF
     */
    public function __construct(Team $team, TeamRepository $TR, IListOfMatchesComponentFactory $LOMCF)
    {
        parent::__construct();
        $this->team = $team;
        $this->TR = $TR;
        $this->LOMCF = $LOMCF;
    }

    public function render()
    {
        $this->template->team = $this->team;
        $this->template->render();
    }

    /**
     * @return ListOfMatchesComponent
     */
    public function createComponentListOfMatchesComponent()
    {
        return $this->LOMCF->create($this->team);
    }
}

interface ITeamDetailComponentFactory
{
    /**
     * @param $team Team
     * @return TeamDetailComponent
     */
    public function create(Team $team);
}