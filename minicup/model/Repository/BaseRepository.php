<?php

namespace Minicup\Model\Repository;

use LeanMapper\Entity;
use LeanMapper\Events;
use LeanMapper\Exception\InvalidStateException;
use LeanMapper\Repository;
use Minicup\Model\Connection;
use Minicup\Model\Entity\BaseEntity;

/**
 * @property Connection $connection
 */
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
            return [];
        }
        $entities = [];
        foreach ($this->createEntities($this->createFluent()->where('[id] IN (%i)', $ids)->fetchAll()) as $entity) {
            $entities[$entity->id] = $entity;
        }
        return $entities;
    }

    /**
     * Removes given entity (or entity with given id) or array of entities or her ids from database
     *
     * @param Entity[]|Entity|int|int[] $arg
     * @return int
     * @throws InvalidStateException
     */
    public function delete($arg)
    {
        $this->events->invokeCallbacks(Events::EVENT_BEFORE_DELETE, $arg);
        if ($arg instanceof Entity) {
            $this->checkEntityType($arg);
            if ($arg->isDetached()) {
                throw new InvalidStateException('Cannot delete detached entity.');
            }
        } elseif (is_array($arg)) {
            /** @var Entity $item */
            foreach ($arg as $item) {
                if ($item instanceof Entity) {
                    $this->checkEntityType($item);
                    if ($item->isDetached()) {
                        throw new InvalidStateException('Cannot delete detached entity.');
                    }
                }
            }
            if (count($arg) === 0) {
                throw new InvalidStateException('Cannot delete by empty argument.');
            }
        }
        $result = $this->deleteFromDatabase($arg);
        if ($arg instanceof Entity) {
            $arg->detach();
        } elseif (is_array($arg)) {
            foreach ($arg as $item) {
                if ($item instanceof Entity) {
                    $item->detach();
                }
            }
        }
        $this->events->invokeCallbacks(Events::EVENT_AFTER_DELETE, $arg);
        return $result;
    }

    /**
     * Performs database delete (can be customized)
     *
     * @param Entity[]|Entity|int|int[] $arg
     * @return int
     */
    protected function deleteFromDatabase($arg)
    {
        $primaryKey = $this->mapper->getPrimaryKey($this->getTable());
        $idField = $this->mapper->getEntityField($this->getTable(), $primaryKey);

        if (is_array($arg)) {
            $ids = array_map(function ($item) use ($idField) {
                return $item instanceof Entity ? $item->$idField : $item;
            }, $arg);
            return $this->connection->query(
                'DELETE FROM %n WHERE %n IN (?)',
                $this->getTable(),
                $primaryKey,
                $ids
            );
        }
        $id = ($arg instanceof Entity) ? $arg->$idField : $arg;
        return $this->connection->query(
            'DELETE FROM %n WHERE %n = ?',
            $this->getTable(),
            $primaryKey,
            $id
        );
    }


}
