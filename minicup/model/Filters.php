<?php

namespace Minicup\Model;


use LeanMapper\Fluent;
use LeanMapper\Reflection\Property;
use Minicup\Model\Entity\Team;

class Filters
{

    /**
     * @param Fluent $statement
     * @param Team $team
     * @param Property $p
     */
    public function joinAllMatches(Fluent $statement, Team $team, Property $p)
    {
        $statement->removeClause('where')->where('[home_team_id] = ', $team->id, 'OR [away_team_id] =', $team->id);
    }

    /**
     * @param Fluent $fluent
     */
    public function joinTeamInfo(Fluent $fluent)
    {
        $fluent->select('[team_info].*')->leftJoin('[team_info]')->on('[team.team_info_id] = [team_info.id]');
    }
}