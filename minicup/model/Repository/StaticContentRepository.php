<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\StaticContent;
use Minicup\Model\Entity\Year;

class StaticContentRepository extends BaseRepository
{
    /**
     * @param string $slug
     * @param Year   $year
     * @return StaticContent|NULL
     */
    public function getBySlug($slug, Year $year)
    {
        $row = $this->createFluent()->where('[slug] = %s', $slug)->where('[year_id] = ', $year->id)->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }
}
