<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\TeamInfo;

class TeamInfoRepository extends BaseRepository
{
    /**
     * @param $category Category|int
     * @param $name string
     * @param $slug string
     * @return TeamInfo
     */
    public function findByCategoryNameSlug($category, $name, $slug)
    {
        if ($category instanceof Category) {
            $category = $category->id;
        }
        $row = $this->createFluent()->where('[category_id] = %i AND [name] = %s AND [slug] = %s', $category, $name, $slug)->fetch();
        if ($row) {
            return $this->createEntity($row);
        }
        return NULL;
    }

    /**
     * @param Category $category
     * @param string   $name
     * @return TeamInfo|NULL
     */
    public function getByName(Category $category, $name)
    {
        $row = $this->createFluent()
            ->where('[category_id] = ', $category->id)
            ->where('[name] LIKE %~like~', $name)
            ->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }

    /**
     * @param Category $category
     * @param string   $slug
     * @return TeamInfo|NULL
     */
    public function getBySlug(Category $category, string $slug)
    {
        $row = $this->createFluent()
            ->where('[category_id] = ', $category->id)
            ->where('[slug] = %s', $slug)
            ->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }

    /**
     * @param $token string
     * @return TeamInfo|NULL
     */
    public function getByToken($token)
    {
        $row = $this->createFluent()
            ->where('[auth_token] = %s', $token)
            ->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }
}
