<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\MatchTerm;

/**
 * @entity MatchTerm
 * @table match_term
 *
 */
class MatchTermRepository extends Repository
{
    /**
     * @param \DibiDateTime $dt
     * @return MatchTerm|null
     */
    public function getByStart(\DibiDateTime $dt)
    {
        $date = clone $dt;
        $time = clone $dt;
        $date->setTime(0, 0, 0);
        $time->setDate(0, 0, 0);
        $row = $this->connection->select('[match_term.*]')->from($this->getTable())
            ->where('[match_term.start] = %s', $time->format('H:i:s'), ' AND [day.day] = %s', $date->format('Y-m-d'))
            ->leftJoin('day')
            ->on('[day.id] = [match_term.day_id]')->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }
}
