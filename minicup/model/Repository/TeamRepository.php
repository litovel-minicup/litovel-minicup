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

    public function findAll()
    {
        return $this->createEntities(
            $this->createFluent()
                ->fetchAll()
        );
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


}
