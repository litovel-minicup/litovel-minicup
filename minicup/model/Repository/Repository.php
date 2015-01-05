<?php

namespace Minicup\Model\Repository;

use LeanMapper\Entity;
use LeanMapper\Exception\Exception;

abstract class Repository extends \LeanMapper\Repository
{

    /**
     * @param $id
     * @return Entity
     * @throws EntityNotFoundException
     */
    public function get($id)
    {
        $row = $this->createFluent()
            ->where('[' . $this->getTable() . '.id] = %i', $id)
            ->fetch();
        if ($row === false) {
            throw new EntityNotFoundException('Entity was not found.');
        }
        return $this->createEntity($row);
    }

    public function findAll()
    {
        return $this->createEntities(
            $this->createFluent()->fetchAll()
        );
    }

}

class EntityNotFoundException extends Exception
{

}
