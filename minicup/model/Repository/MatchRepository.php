<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\Team;

class MatchRepository extends BaseRepository
{
    /**
     * @param Category $category
     * @return Match[]
     */
    public function findMatchesByCategory(Category $category)
    {
        return $this->createEntities(
            $this->createFluent()
                ->applyFilter('confirmed')
                ->where('[match.category_id] = %i', $category->id)->fetchAll());
    }

    /**
     * @param $t Team
     * @return Match[]
     * @deprecated
     */
    public function findMatchesToTeam(Team $t)
    {
        $rows = $this->connection->select('*')->from($this->getTable())->where('[home_team_id] = ', $t->id, 'OR [away_team_id] = ', $t->id)->fetchAll();
        return $this->createEntities($rows);
    }

    /**
     * @param Team $team1
     * @param Team $team2
     * @return Match|NULL
     */
    public function getCommonMatchForTeams(Team $team1, Team $team2)
    {
        $team1InfoId = $team1->i->id;
        $team2InfoId = $team2->i->id;
        $row = $this->createFluent()
            ->where('(
                ([home_team_info_id] = %i AND [away_team_info_id] = %i) OR
                ([home_team_info_id] = %i AND [away_team_info_id] = %i)
            ) AND [confirmed] = 1',
                $team1InfoId, $team2InfoId, $team2InfoId, $team1InfoId)->fetch();
        if ($row) {
            return $this->createEntity($row);
        }
        return NULL;
    }
}
