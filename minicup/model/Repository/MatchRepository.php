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
     * @throws \Dibi\Exception
     */
    public function getCurrentMatches(Category $category, $limit = 0)
    {
        $dt = new DateTime();
        $date = clone $dt;
        $time = clone $dt;
        $date->setTime(0, 0, 0);
        $time->setDate(0, 0, 0);

        return $this->createEntities($this->connection->query('
            SELECT DISTINCT `match`.*
            FROM `match`
              LEFT JOIN `match_term` AS `mt` ON `match`.`match_term_id` = mt.`id`
              LEFT JOIN `day` AS `d` ON d.`id` = mt.`day_id`
            WHERE
              `match`.`category_id` = %i
              AND
              `d`.`day` = %s
              AND
              (
               (`mt`.`start` < %s AND `mt`.`end` > %s)
               OR
                `online_state` IN %in
              )
            
            ORDER BY d.`day` ASC, mt.`start` ASC, `mt`.`location` ASC, `match`.`id` ASC 
            LIMIT %i
            ',
            $category->id,
            $date->format('Y-m-d'),
            $time->format('H:i:s'),
            $time->format('H:i:s'),
            Match::ONLINE_STATE_PLAYING,
            $limit
        )->fetchAll()
        );
    }


    /**
     * @param Category $category
     * @param int      $limit
     * @return Match[]
     */
    public function findNextMatches(Category $category, $limit = 0)
    {
        $fluent = $this->createCategoryFluent($category, $limit);
        $dt = new DateTime();
        $fluent = $fluent
            ->where('TIMESTAMP([mt.start]) + TIMESTAMP([d.day]) > %i', $dt->getTimestamp())
            ->where('[confirmed] IS NULL')
            ->where('[online_state] NOT IN %l', Match::ONLINE_STATE_PLAYING)
            ->where('[online_state] != %s', Match::END_ONLINE_STATE);
        return $this->createEntities($fluent->fetchAll());
    }

    /**
     * @param Category $category
     * @param int      $limit
     * @return Match[]
     */
    public function findLastMatches(Category $category, $limit = 0): array
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
     * @param Team      $team1
     * @param Team      $team2
     * @param bool|null $confirmed
     * @return Match|NULL
     */
    public function getCommonMatchForTeams(Team $team1, Team $team2, ?bool $confirmed = true): ?Match
    {
        $team1InfoId = $team1->i->id;
        $team2InfoId = $team2->i->id;
        $f = $this->createFluent()
            ->where('(
                ([home_team_info_id] = %i AND [away_team_info_id] = %i) OR
                ([home_team_info_id] = %i AND [away_team_info_id] = %i)
            )',
                $team1InfoId, $team2InfoId, $team2InfoId, $team1InfoId
            );
        if ($confirmed === TRUE)
            $f = $f->where('[confirmed] IS NOT NULL');
        if ($confirmed === FALSE)
            $f = $f->where('[confirmed] IS NULL');

        $row = $f->fetch();
        if ($row) {
            return $this->createEntity($row);
        }
        return NULL;
    }

    /**
     * @param Category $category
     * @return array
     */
    public function groupMatchesByDay(Category $category): array
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
     * @throws \LeanMapper\Exception\InvalidStateException
     */
    public function findMatchesConfirmedAfterMatchIncluded(Match $match): array
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
     * @throws \LeanMapper\Exception\InvalidStateException
     */
    public function getMatchConfirmedBeforeMatchExcluded(Match $match): ?Match
    {
        $row = $this->connection->select('*')
            ->from($this->getTable())
            ->where('[category_id] = ', $match->category->id)
            ->where('[confirmed_as] = ', $match->confirmedAs - 1)->fetch();

        return $row ? $this->createEntity($row) : NULL;
    }

    /**
     * @param Category $category
     * @return Match|NULL
     */
    public function getFirstMatchInCategory(Category $category): ?Match
    {
        $row = $this->createCategoryFluent($category)
            ->leftJoin('[match_term] ON [match_term].[id] = [match].[id]')
            ->leftJoin('[day] ON [match_term].[day_id] = [day].[id]')
            ->orderBy('[match_term].[start] ASC')->limit(1)->fetch();

        return $row ? $this->createEntity($row) : NULL;
    }
}
