<?php

namespace Minicup\Model\Repository;

use LeanMapper\Entity;
use LeanMapper\Exception\InvalidStateException;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;

class TeamRepository extends BaseRepository
{
    /**
     * @param $slug string
     * @param $category Category
     * @return Team|NULL
     */
    public function getBySlug($slug, Category $category)
    {
        $row = $this->createFluent()
            ->where('[team_info.slug] = %s', $slug, 'AND [team.category_id] = ', $category->id)
            ->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }

    /**
     * @param Team $team
     * @return Team[]
     */
    public function findHistoricalTeams(Team $team)
    {
        // without using $this->createFluent(), because in createFluent is applied actual filter
        $id = $team->i->id;
        $rows = $this->connection->query("
          SELECT * FROM {$this->getTable()}
            WHERE [team.team_info_id] = %i
              AND [team.after_match_id] IN
                (SELECT [match.id] FROM [match] WHERE [match.home_team_info_id] = %i OR [match.away_team_info_id] = %i)",
            $id, $id, $id)->fetchAll();
        return $this->createEntities($rows);
    }

    /**
     * persist team
     * TODO: persisting TeamInfo together with Team!
     * @param Entity $entity
     * @return int
     * @throws \DibiException
     * @throws InvalidStateException
     */
    public function persist(Entity $entity)
    {
        if (!($entity instanceof Team)) {
            throw new InvalidStateException("Instance of '".Team::getReflection()->name."' expected, '".$entity->getReflection()->name."' given.");
        }
        return parent::persist($entity);
    }

    /**
     * @param Category $category
     * @return Team[]
     */
    public function getByCategory(Category $category)
    {
        $rows = $this->createFluent()->applyFilter('actual')
            ->where('[team.category_id] = %i', $category->id)
            ->fetchAll();
        return $this->createEntities($rows);
    }
}
