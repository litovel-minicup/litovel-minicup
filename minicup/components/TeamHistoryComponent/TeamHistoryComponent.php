<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Team;

class TeamHistoryComponent extends BaseComponent
{
    /** @var Team $team */
    private $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function render()
    {
        parent::render();
    }
}

interface ITeamHistoryComponent
{
    /**
     * @param Team $team
     * @return TeamHistoryComponent
     */
    public function create(Team $team);

}