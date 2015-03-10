<?php

namespace Minicup\Model\Repository;


use Minicup\Model\Entity\News;

class NewsRepository extends BaseRepository
{
    /**
     * Default limit for news finding.
     */
    const DEFAULT_LIMIT = 5;

    /**
     * @param int $limit
     * @return News[]
     */
    public function findLastNews($limit = NewsRepository::DEFAULT_LIMIT)
    {
        return $this->createEntities($this->createFluent()->limit($limit)->fetchAll());
    }
}