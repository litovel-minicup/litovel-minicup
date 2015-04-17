<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\TeamRepository;

class TeamHistoryComponent extends BaseComponent
{
    /** @var Team $team */
    private $team;

    /** @var TeamRepository */
    private $TR;

    public function __construct(Team $team, TeamRepository $TR)
    {
        $this->team = $team;
        $this->TR = $TR;
    }

    public function render()
    {
        $this->template->team = $this->team;
        parent::render();
    }

    public function handleData()
    {
        $data = array("labels" => array(), "series" => array(array()));
        foreach (array_slice($this->TR->findHistoricalTeams($this->team), 1) as $team) {
            $data["labels"][] = $team->afterMatch->id;
            $data["series"][0][] = $team->order;
        }
        $this->presenter->sendJson($data);
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