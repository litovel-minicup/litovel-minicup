<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\MatchEvent;
use Minicup\Model\Entity\Player;
use Minicup\Model\Entity\TeamInfo;

class PlayerRepository extends BaseRepository
{
    /**
     * @param TeamInfo $teamInfo
     * @return Player[]
     * @throws \Dibi\Exception
     */
    public function findByTeamWithConfirmedGoals(TeamInfo $teamInfo)
    {
        $f = $this->connection->query('
            select
              p.*,
              count(me.id) as goals
            from player as p
              left join match_event me on p.id = me.player_id
              left join `match` m on me.match_id = m.id
              where p.team_info_id = %i and (me.id is null or m.confirmed is not null) and (me.id is null or me.type = %s)
            group by p.id
            order by secondary_number, number, p.id;
        ', $teamInfo->id, MatchEvent::TYPE_GOAL);

        return $this->createEntities($f->fetchAll());
    }
}
