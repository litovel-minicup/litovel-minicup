<?php

namespace Minicup\Model\Repository;


use Minicup\Model\Entity\Tag;
use Minicup\Model\Entity\Year;

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
     * @param Year   $year
     * @return Tag[]
     */
    public function findLikeTerm($term = NULL, Year $year = NULL)
    {
        $fluent = $this->createFluent();
        if ($term) {
            $fluent->where('[slug] LIKE %~like~', $term);
        }
        if ($year) {
            $fluent->where('[year_id] = ', $year->id);
        }
        return $this->createEntities($fluent->fetchAll());
    }

    /**
     * @param Year $year
     * @return Tag[]
     */
    public function findMainTags(Year $year = NULL)
    {
        $fluent = $this->createFluent()->where('[is_main] = 1');
        if ($year) {
            $fluent->where('[year_id] = ', $year->id);
        }
        return $this->createEntities($fluent->fetchAll());
    }
}