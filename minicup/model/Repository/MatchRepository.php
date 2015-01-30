<?php

namespace Minicup\Model\Repository;

use LeanMapper\Fluent;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\Team;

/**
 * Class MatchRepository
 * @package Minicup\Model\Repository
 */
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
        $fluent = $this->createCategoryFluent($category);
        if ($mode == static::CONFIRMED) {
            $fluent->applyFilter('confirmed');
        } elseif ($mode == static::UNCONFIRMED) {
            $fluent->applyFilter('unconfirmed');
        }
        return $this->createEntities($fluent->fetchAll());
    }

    /**
     * @param Category  $category
     * @param int       $limit
     * @return Match[]
     */
    public function getCurrentMatches(Category $category, $limit = 0)
    {
        $fluent = $this->createCategoryFluent($category, $limit);
        $dt = new \DibiDateTime();
        $date = clone $dt;
        $time = clone $dt;
        $date->setTime(0, 0, 0);
        $time->setDate(0, 0, 0);
        $fluent = $fluent
            ->where('[mt.start] < %s AND [mt.end] > %s', $time->format('H:i:s'), $time->format('H:i:s'))
            ->where('[d.day] = %s', $date->format('Y-m-d'));
        return $this->createEntities($fluent->fetchAll());
    }

    /**
     * @param Category  $category
     * @param int       $limit
     * @return Match[]
     */
    public function getNextMatches(Category $category, $limit = 0)
    {
        $fluent = $this->createCategoryFluent($category, $limit);
        $dt = new \DibiDateTime();
        // TODO: repair this fucking datetimes!
        $fluent = $fluent
            ->where('TIMESTAMP([mt.start])+TIMESTAMP([d.day]) > %i', $dt->getTimestamp());
        return $this->createEntities($fluent->fetchAll());
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

    /**
     * provide to fluent aliases 'mt'(match_term) and 'd'(day) joined to match
     * @param Category $category
     * @param string $order
     * @param int $limit
     * @return Fluent
     */
    private function createCategoryFluent(Category $category, $limit = 0, $order = BaseRepository::ORDER_ASC)
    {
        $fluent = $this->createFluent($order)->where('[match.category_id] = %i', $category->id);
        if ($limit) {
            $fluent->limit($limit);
        }

        return $fluent;
    }
}
