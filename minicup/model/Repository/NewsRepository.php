<?php

namespace Minicup\Model\Repository;


use Minicup\Model\Entity\News;
use Minicup\Model\Entity\Year;

class NewsRepository extends BaseRepository
{
    /**
     * Default limit for news finding.
     */
    const DEFAULT_LIMIT = 5;

    /**
     * @param int  $limit
     * @param Year $year
     * @return News[]
     */
    public function findLastNews(Year $year, $limit = NewsRepository::DEFAULT_LIMIT)
    {
        return $this->createEntities(
            $this->createFluent()->where('[year_id] =', $year->id)->where('[published] = 1')->limit($limit)->fetchAll()
        );
    }

    /**
     * @param Year $year
     * @return int
     */
    public function getNewsCountInYear(Year $year)
    {
        return $this->createFluent()->where('[year_id] =', $year->id)->where('[published] = 1')->count();
    }
}