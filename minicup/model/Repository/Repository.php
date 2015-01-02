<?php

namespace Minicup\Model\Repository;

abstract class Repository extends \LeanMapper\Repository
{

    public function find($id)
    {
        $row = $this->createFluent()
            ->where('id = %i', $id)
            ->fetch();

        if ($row === false) {
            throw new \Exception('Entity was not found.');
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
