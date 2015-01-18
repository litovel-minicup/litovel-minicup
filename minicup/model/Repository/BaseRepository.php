<?php

namespace Minicup\Model\Repository;

use LeanMapper\Entity;
use LeanMapper\Exception\Exception;
use LeanMapper\Repository;

abstract class BaseRepository extends Repository
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

    /**
     * @return Entity[]
     */
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
