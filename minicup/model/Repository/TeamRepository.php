<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\Team;

class TeamRepository extends Repository
{
    /**
     * @param $slug string
     * @return Team|NULL
     */
    public function getBySlug($slug)
    {
        $row = $this->connection->select('*')->from($this->getTable())->where('slug = %s', $slug)->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }
}
