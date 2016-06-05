<?php

namespace Minicup\Model\Repository;

use Dibi\DateTime;
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
     * @param string   $mode
     * @return Match[]
     */
    public function findMatchesByCategory(Category $category, $mode = MatchRepository::BOTH)
    {
        $fluent = $this->createCategoryFluent($category);
        if ($mode === static::CONFIRMED) {
            $fluent->applyFilter($mode);
        } elseif ($mode === static::UNCONFIRMED) {
            $fluent->applyFilter($mode);
        }
        return $this->createEntities($fluent->fetchAll());
    }

    /**
     * provide to fluent aliases 'mt'(match_term) and 'd'(day) joined to match
     * @param Category $category
     * @param string   $order
     * @param int      $limit
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

    /**
     * @param Category $category
     * @param int      $limit
     * @return Match[]
     */
    public function getCurrentMatches(Category $category, $limit = 0)
    {
        $fluent = $this->createCategoryFluent($category, $limit);
        $dt = new DateTime();
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
     * TODO: remove actual playing matches from select
     *
     * @param Category $category
     * @param int      $limit
     * @return Match[]
     */
    public function getNextMatches(Category $category, $limit = 0)
    {
        $fluent = $this->createCategoryFluent($category, $limit);
        $dt = new DateTime();
        $fluent = $fluent
            ->where('TIMESTAMP([mt.start])+TIMESTAMP([d.day]) > %i', $dt->getTimestamp())
            ->where('[confirmed] IS NULL');
        return $this->createEntities($fluent->fetchAll());
    }

    /**
     * @param Category $category
     * @param int      $limit
     * @return Match[]
     */
    public function getLastMatches(Category $category, $limit = 0)
    {
        $fluent = $this
            ->createCategoryFluent($category, $limit, BaseRepository::ORDER_DESC)
            ->where('[confirmed] IS NOT NULL');
        if ($limit) {
            $fluent->limit($limit);
        }
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
            ) AND [confirmed] IS NOT NULL',
                $team1InfoId, $team2InfoId, $team2InfoId, $team1InfoId)->fetch();
        if ($row) {
            return $this->createEntity($row);
        }
        return NULL;
    }

    /**
     * @param Category $category
     * @return array
     */
    public function groupMatchesByDay(Category $category)
    {
        $days = [];
        foreach ($category->matches as $match) {
            $days[$match->matchTerm->day->day->getTimestamp()][] = $match;
        }
        return $days;
    }

    /**
     * @param Match $match
     * @return Match[]
     */
    public function findMatchesConfirmedAfterMatchIncluded(Match $match)
    {
        $f = $this->connection->select('*')->from($this->getTable())
            ->where('[category_id] = ', $match->category->id)
            ->where('[confirmed] IS NOT NULL')
            ->where('[confirmed_as] >= ', $match->confirmedAs)
            ->orderBy('[confirmed_as] ', BaseRepository::ORDER_ASC);

        return $this->createEntities($f->fetchAll());
    }

    /**
     * @param Match $match
     * @return Match|NULL
     */
    public function getMatchConfirmedBeforeMatchExcluded(Match $match)
    {
        $row = $this->connection->select('*')
            ->from($this->getTable())
            ->where('[category_id] = ', $match->category->id)
            ->where('[confirmed_as] = ', $match->confirmedAs - 1)->fetch();

        return $row ? $this->createEntity($row) : NULL;
    }
}
