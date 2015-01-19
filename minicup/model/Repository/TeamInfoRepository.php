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
     * @throws EntityNotFoundException
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
        throw new EntityNotFoundException('Team info not found');
    }
}
