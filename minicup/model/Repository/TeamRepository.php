<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;

class TeamRepository extends Repository
{
    /**
     * @param $slug string
     * @param $category Category
     * @return Team|NULL
     */
    public function getBySlug($slug, Category $category)
    {
        $row = $this->connection->select('*')->from($this->getTable())->where('slug = %s', $slug, 'AND [category_id] = ', $category->id)->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }

}
