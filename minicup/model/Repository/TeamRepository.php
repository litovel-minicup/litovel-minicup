<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\TeamInfo;

class TeamRepository extends BaseRepository
{
    /**
     * @param $arg string|TeamInfo|Team
     * @param $category Category
     * @return Team|NULL
     */
    public function getBySlug($arg, Category $category)
    {
        if ($arg instanceof Team) {
            return $arg;
        } elseif ($arg instanceof TeamInfo) {
            return $arg->team;
        }
        $row = $this->createFluent()
            ->where('[team_info.slug] = %s', $arg, 'AND [team.category_id] = ', $category->id)
            ->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }

    /**
     * @param TeamInfo $teamInfo
     * @return Team[]
     */
    public function findHistoricalTeams(TeamInfo $teamInfo)
    {
        // without using $this->createFluent(), because in createFluent is applied actual filter
        $id = $teamInfo->id;
        $rows = $this->connection->query("SELECT [team.*] FROM {$this->getTable()}
            WHERE
              [team.team_info_id] = %i
            AND
              (

                ([team.after_match_id] IN (
                  SELECT [match.id] FROM [match]
                  WHERE ([match.home_team_info_id] = %i OR [match.away_team_info_id] = %i)
                  AND [match.confirmed] IS NOT NULL)
                )
              )", $id, $id, $id)->fetchAll();

        return $this->createEntities($rows);
    }

    /**
     * @param Category $category
     * @param bool     $onlyActual
     * @return \Minicup\Model\Entity\Team[]
     */
    public function getByCategory(Category $category, $onlyActual = TRUE)
    {
        $fluent = $this->connection->select('[team.*]')->from($this->getTable())
            ->where('[team.category_id] = %i', $category->id);
        if ($onlyActual) {
            $fluent->where('[team.actual] = 1');
        }

        return $this->createEntities($fluent->fetchAll());
    }

    /**
     * @param Category $category
     * @return Team[]
     */
    public function findInitTeams(Category $category)
    {
        $f = $this->connection->select('*')->from($this->getTable())
            ->where('[category_id] = ', $category->id)
            ->where('[after_match_id] IS NULL');

        return $this->createEntities($f->fetchAll());
    }
}
