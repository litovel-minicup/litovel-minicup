<?php

namespace Minicup\Model\Repository;


use Minicup\Model\Entity\Tag;

class TagRepository extends BaseRepository
{
    /**
     * @param $slug
     * @return Tag
     * @throws EntityNotFoundException
     */
    public function getBySlug($slug)
    {
        $row = $this->createFluent()->where('[slug] = %s', $slug)->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }
}