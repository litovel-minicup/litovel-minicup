<?php

namespace Minicup\Model;


use LeanMapper\Fluent;
use Minicup\Model\Entity\Team;
use Nette\Object;

class Filters extends Object
{

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
        $fluent->select('[team_info.name], [team_info.slug]')->leftJoin('[team_info]')->on('[team.team_info_id] = [team_info.id]');
    }

    /**
     * @param Fluent $fluent
     */
    public function actualTeams(Fluent $fluent)
    {
        $fluent->where('[team.is_actual] = 1');
    }

    /**
     * @param Fluent $fluent
     */
    public function orderedTeams(Fluent $fluent)
    {
        $fluent->orderBy('[team.order] ASC');
    }
}