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

    /** @var IStaticContentComponentFactory */
    private $SCCF;

    /**
     * @param Team $team
     * @param TeamRepository $TR
     * @param IListOfMatchesComponentFactory $LOMCF
     * @param IStaticContentComponentFactory $SCCF
     */
    public function __construct(Team $team, TeamRepository $TR, IListOfMatchesComponentFactory $LOMCF, IStaticContentComponentFactory $SCCF)
    {
        parent::__construct();
        $this->team = $team;
        $this->TR = $TR;
        $this->LOMCF = $LOMCF;
        $this->SCCF = $SCCF;
    }

    public function render()
    {
        $this->template->team = $this->team;
        parent::render();
    }

    /**
     * @return ListOfMatchesComponent
     */
    public function createComponentListOfMatchesComponent()
    {
        return $this->LOMCF->create($this->team);
    }

    /**
     * @return StaticContentComponent
     */
    public function createComponentStaticContentComponent()
    {
        return $this->SCCF->create($this->team);
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