<?php

namespace Minicup\Model\Repository;

use LeanMapper\Fluent;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\Team;

class MatchRepository extends BaseRepository
{
    /** constants for getting matches from db */
    const CONFIRMED = 'confirmed';
    const UNCONFIRMED = 'unconfirmed';
    const BOTH = 'both';


    /**
     * @param Category $category
     * @param string $mode
     * @return Match[]
     */
    public function findMatchesByCategory(Category $category, $mode = MatchRepository::BOTH)
    {
        /** @var Fluent $fluent */
        $fluent = $this->createFluent()->where('[match.category_id] = %i', $category->id);
        if ($mode == static::CONFIRMED) {
            $fluent->applyFilter('confirmed');
        } elseif ($mode == static::UNCONFIRMED) {
            $fluent->applyFilter('unconfirmed');
        }
        return $this->createEntities($fluent->fetchAll());
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
