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
        $row = $this->connection->select('*')->from($this->getTable())->where('start =', $dt)->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }
}
