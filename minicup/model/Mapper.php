<?php

namespace Minicup\Model;

use LeanMapper\DefaultMapper;
use LeanMapper\Row;

class Mapper extends DefaultMapper {

    public function __construct($NS) {
        $this->defaultEntityNamespace = $NS;
    }

    public function getEntityClass($table, Row $row = null) {
        if ($table == 'online_report') { // TODO: fuj, fuj
            return 'Minicup\Model\Entity\OnlineReport';
        } elseif ($table == 'match_term') {
            return 'Minicup\Model\Entity\MatchTerm';
        }
        return parent::getEntityClass($table, $row);
    }

}
