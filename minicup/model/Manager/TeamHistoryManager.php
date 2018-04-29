<?php

namespace Minicup\Model\Manager;


use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Repository\TeamRepository;
use Nette\SmartObject;

class TeamHistoryManager
{
    use SmartObject;

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

    /**
     * Return history for team composed from TeamHistoryRecords.
     * @param TeamInfo $teamInfo
     * @return TeamHistoryRecord[]
     */
    public function getSingleHistoryForTeam(TeamInfo $teamInfo)
    {
        $data = [];
        /** @var Team $team */
        foreach ($this->teamRepository->findHistoricalTeams($teamInfo) as $team) {
            $data[] = new TeamHistoryRecord(
                $team,
                $team->afterMatch->getRival($teamInfo)
            );

        }
        return $data;
    }

    /**
     * @param TeamInfo[] $teams
     * @return TeamHistoryRecord[][]
     */
    public function getHistoryForTeams(array $teams)
    {
        $data = [];
        foreach ($teams as $teamInfo) {
            $data[$teamInfo->id] = [];
        }

        foreach ($teams as $teamInfo) {
            foreach ($this->teamRepository->findHistoricalTeams($teamInfo) as $historicalTeam) {
                $data[$teamInfo->id][] = new TeamHistoryRecord(
                    $historicalTeam,
                    $historicalTeam->afterMatch->getRival($teamInfo)
                );
            }
        }

        $maxRecords = max(array_map(function ($line) {
            return count($line);
        }, $data));
        foreach ($data as & $line) {
            $line = array_pad($line, $maxRecords, NULL);
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
     * @param Team     $team
     * @param TeamInfo $againstTeam
     */
    public function __construct(Team $team,
                                TeamInfo $againstTeam)
    {
        $this->team = $team;
        $this->againstTeam = $againstTeam;
        $this->order = $team->order;
    }
}