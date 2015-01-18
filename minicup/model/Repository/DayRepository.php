<?php

namespace Minicup\Model\Repository;
use Minicup\Model\Entity\MatchTerm;

class DayRepository extends BaseRepository
{
    /**
     * @param \DibiDateTime $dt
     * @return MatchTerm|null
     */
    public function getByDatetime(\DibiDateTime $dt)
    {
        $row = $this->connection->select('*')->from($this->getTable())->where('[day] = %s', $dt->format('Y-m-d'))->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }
}
