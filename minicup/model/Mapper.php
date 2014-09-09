<?php
namespace Minicup\Model;

class Mapper extends \LeanMapper\DefaultMapper {
    public function __construct() {
        $this->defaultEntityNamespace = 'Minicup\Model\Entity'; # TODO to config.neon
    }
}
