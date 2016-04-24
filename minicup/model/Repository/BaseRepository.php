<?php

namespace Minicup\Model\Repository;

use LeanMapper\Repository;
use Minicup\Model\Entity\BaseEntity;

abstract class BaseRepository extends Repository
{

    /** constants for ordering options  */
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    /** constants for selecting from array of conditions */
    const METHOD_OR = 'OR';
    const METHOD_AND = 'AND';

    /**
     * @param string   $event
     * @param callable $callback
     */
    public function registerCallback($event, $callback)
    {
        $this->events->registerCallback($event, $callback);
    }

    /**
     * @param      $id
     * @param bool $useFilters
     * @return BaseEntity|NULL
     */
    public function get($id, $useFilters = TRUE)
    {
        if ($useFilters) {
            $f = $this->createFluent();
        } else {
            $f = $this->connection->select('[' . $this->getTable() . '.*]')->from($this->getTable());
        }
        $row = $f
            ->where('[' . $this->getTable() . '.id] = %i', $id)
            ->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }

    /**
     * @param bool|TRUE $withFilters
     * @return array
     */
    public function findAll($withFilters = TRUE)
    {
        if ($withFilters) {
            return $this->createEntities(
                $this->createFluent()->fetchAll()
            );
        } else {
            return $this->createEntities(
                $this->connection->select('*')->from($this->getTable())->fetchAll()
            );
        }
    }

    /**
     * @param int[] $ids
     * @return BaseEntity[]
     */
    public function findByIds(array $ids)
    {
        if (!$ids) {
            return array();
        }
        $entities = array();
        foreach ($this->createEntities($this->createFluent()->where('[id] IN (%i)', $ids)->fetchAll()) as $entity) {
            $entities[$entity->id] = $entity;
        }
        return $entities;
    }


}
