<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\TeamRepository;
use Minicup\Model\TeamHistoryManager;
use Minicup\Model\TeamHistoryRecord;

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

    /**  @var TeamHistoryManager */
    private $teamHistoryManager;

    public function __construct(Team $team,
                                TeamRepository $TR, TeamHistoryManager $teamHistoryManager)
    {
        $this->team = $team;
        $this->TR = $TR;
        $this->teamHistoryManager = $teamHistoryManager;
        parent::__construct();
    }

    public function render()
    {
        $this->template->team = $this->team;
        parent::render();
    }

    public function handleData()
    {
        $data = ['labels' => [], 'series' => [[]]];
        $history = $this->teamHistoryManager->getSingleHistoryForTeam($this->team->i);
        $teamsInCategory = count($this->team->category->teams);
        foreach ($history as $record) {
            /** @var TeamHistoryRecord $record */
            $data['labels'][] = $record->againstTeam->name;
            $data['series'][0][] = $teamsInCategory + 1 - $record->team->order;
        }
        $this->presenter->sendJson($data);
    }
}