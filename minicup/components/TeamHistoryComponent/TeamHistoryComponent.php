<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\TeamRepository;

interface ITeamHistoryComponent
{
    /**
     * @param Team $team
     * @return TeamHistoryComponent
     */
    public function create(Team $team);

}

class TeamHistoryComponent extends BaseComponent
{
    /** @var Team $team */
    private $team;

    /** @var TeamRepository */
    private $TR;

    public function __construct(Team $team,
                                TeamRepository $TR)
    {
        $this->team = $team;
        $this->TR = $TR;
        parent::__construct();
    }

    public function render()
    {
        $this->template->team = $this->team;
        parent::render();
    }

    public function handleData()
    {
        $data = array('labels' => array(), 'series' => array(array()));
        /** @var Team $team */
        foreach ($this->TR->findHistoricalTeams($this->team) as $team) {
            $data['labels'][] = ($team->afterMatch->homeTeam->id === $this->team->i->id) ? $team->afterMatch->awayTeam->name : $team->afterMatch->homeTeam->name;
            $data['series'][0][] = count($team->category->teams) - $team->order;
        }
        $this->presenter->sendJson($data);
    }
}