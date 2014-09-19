<?php

namespace Minicup\Model;

class Mapper extends \LeanMapper\DefaultMapper {

    public function __construct($NS) {
        $this->defaultEntityNamespace = $NS;
    }

    public function getEntityClass($table, \LeanMapper\Row $row = null) {
        if ($table == 'online_report') { // TODO: fuj, fuj
            return 'Minicup\Model\Entity\OnlineReport';
        }
        return parent::getEntityClass($table, $row);
    }

}
