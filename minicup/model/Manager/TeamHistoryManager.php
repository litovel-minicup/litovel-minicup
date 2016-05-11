<?php

namespace Minicup\Model;


use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Repository\TeamRepository;
use Nette\SmartObject;

class TeamHistoryManager
{
    /** @var TeamRepository */
    private $teamRepository;

    /**
     * TeamHistoryManager constructor.
     * @param TeamRepository $teamRepository
     */
    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }


    public function getSingleHistoryForTeam(TeamInfo $teamInfo)
    {
        $data = [];
        /** @var Team $team */
        foreach ($this->teamRepository->findHistoricalTeams($teamInfo->team) as $team) {
            $data[] = new TeamHistoryRecord(
                $team,
                $team->afterMatch->homeTeam->id === $teamInfo->id ?
                    $team->afterMatch->awayTeam : $team->afterMatch->homeTeam
            );

        }
        return $data;
    }
}


class TeamHistoryRecord
{
    use SmartObject;
    /** @var Team */
    public $team;
    /** @var int */
    public $order;
    /** @var TeamInfo */
    public $againstTeam;

    /**
     * TeamHistoryRecord constructor.
     * @param Team     $teamRecord
     * @param TeamInfo $againstTeam
     */
    public function __construct(Team $teamRecord, TeamInfo $againstTeam)
    {
        $this->team = $teamRecord;
        $this->againstTeam = $againstTeam;
        $this->order = $teamRecord->order;
    }
}