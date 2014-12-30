<?php

namespace Minicup\Model\Repository;
use Minicup\Model\Entity\MatchTerm;

class DayRepository extends Repository
{
    /**
     * @param \DibiDateTime $dt
     * @return MatchTerm|null
     */
    public function getByDate(\DibiDateTime $dt)
    {
        $row = $this->connection->select('*')->from($this->getTable())->where('[day] = ', $dt->format('Y-m-d'))->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }
}
