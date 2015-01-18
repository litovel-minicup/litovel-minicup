<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\Team;

class MatchRepository extends BaseRepository
{
    /**
     * @param $t Team
     * @return Match[]
     * @deprecated
     */
    public function findMatchesToTeam(Team $t)
    {
        $values = $this->connection->select('*')->from($this->getTable())->where('[home_team_id] = ', $t->id, 'OR [away_team_id] = ', $t->id)->fetchAll();
        return $this->createEntities($values);
    }
}
