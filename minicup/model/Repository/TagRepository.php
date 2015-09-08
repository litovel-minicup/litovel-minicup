<?php

namespace Minicup\Model\Repository;


use Minicup\Model\Entity\Tag;

class TagRepository extends BaseRepository
{
    /**
     * @param $slug
     * @return Tag
     */
    public function getBySlug($slug)
    {
        $row = $this->createFluent()->where('[slug] = %s', $slug)->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }

    /**
     * @param string[] $slugs
     * @return Tag[]
     */
    public function findBySlugs(array $slugs)
    {
        $rows = $this->createFluent()->where('[slug] IN (%s)', $slugs)->fetchAll();
        return $this->createEntities($rows);
    }

    /**
     * @param string $term
     * @return Tag[]
     */
    public function findLikeTerm($term)
    {
        $rows = $this->createFluent()->where('[slug] LIKE %~like~', $term)->fetchAll();
        return $this->createEntities($rows);
    }

    /**
     * @return Tag[]
     */
    public function findMainTags()
    {
        $rows = $this->createFluent()->where('[is_main] = 1')->fetchAll();
        return $this->createEntities($rows);
    }
}