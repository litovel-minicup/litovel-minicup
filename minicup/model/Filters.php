<?php

namespace Minicup\Model;


use LeanMapper\Fluent;
use Minicup\Model\Entity\Team;
use Nette\Object;

class Filters extends Object
{
    /**
     * @param Fluent $fluent
     * @param int $yearId
     */
    public function yearRestrict(Fluent $fluent, $yearId = 0)
    {
        if ($yearId) {
            $fluent->where('[year_id] = %i', $yearId);
        }
    }

    /**
     * @param Fluent $fluent
     * @param Team $team
     */
    public function joinAllMatches(Fluent $fluent, Team $team)
    {
        $fluent->removeClause('where')->where('[home_team_id] = ', $team->id, 'OR [away_team_id] =', $team->id);
    }

    /**
     * @param Fluent $fluent
     */
    public function joinTeamInfo(Fluent $fluent)
    {
        $fluent->leftJoin('[team_info]')->on('[team.team_info_id] = [team_info.id]')->select('[team_info.name], [team_info.slug]');
    }

    /**
     * @param Fluent $fluent
     */
    public function actualTeam(Fluent $fluent)
    {
        $fluent->where('[team.actual] = 1');
    }

    /**
     * @param Fluent $fluent
     */
    public function orderedTeams(Fluent $fluent)
    {
        $fluent->orderBy('[team.order] DESC');
    }

    /**
     * @param Fluent $fluent
     */
    public function confirmedMatch(Fluent $fluent)
    {
        $fluent->where('[match.confirmed] = 1');
    }

    /**
     * @param Fluent $fluent
     */
    public function unconfirmedMatch(Fluent $fluent)
    {
        $fluent->where('[match.confirmed] = 0');
    }
}