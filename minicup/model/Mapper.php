<?php
namespace Minicup\Model;

class Mapper extends \LeanMapper\DefaultMapper {
    public function __construct($NS) {
        $this->defaultEntityNamespace = $NS;
    }
}
