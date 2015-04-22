<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\StaticContent;

class StaticContentRepository extends BaseRepository
{
    /**
     * @param string $slug
     * @return StaticContent|NULL
     */
    public function getBySlug($slug)
    {
        $row = $this->createFluent()->where('[slug] = %s', $slug)->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }
}
