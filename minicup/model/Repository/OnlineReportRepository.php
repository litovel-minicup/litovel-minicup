<?php

namespace Minicup\Model\Repository;

/**
 * @entity OnlineReport
 * @table online_report
 * 
 */
class OnlineReportRepository extends Repository {

    /**
     * @param \Minicup\Model\Entity\Match $match
     * @return \Minicup\Model\Entity\OnlineReport[]
     */
    public function findByMatch(\Minicup\Model\Entity\Match $match) {
        return $this->createEntities($this->connection->select('*')
                                ->from($this->getTable())
                                ->where('match_id = %i', $match->id)
                                ->fetchAll()
        );
    }

}
