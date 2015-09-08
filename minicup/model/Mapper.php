<?php

namespace Minicup\Model;

use LeanMapper\Caller;
use LeanMapper\DefaultMapper;
use LeanMapper\Exception\InvalidStateException;
use LeanMapper\ImplicitFilters;
use LeanMapper\Reflection\Property;
use LeanMapper\Row;

class Mapper extends DefaultMapper
{

    public function __construct($NS)
    {
        $this->defaultEntityNamespace = $NS;
    }


    public function getTable($entityClass)
    {
        return self::toUnderScore($this->trimNamespace($entityClass));
    }

    public static function toUnderScore($str)
    {
        return lcfirst(preg_replace_callback('#(?<=.)([A-Z])#', function ($m) {
            return '_' . strtolower($m[1]);
        }, $str));
    }

    public function getEntityClass($table, Row $row = NULL)
    {
        return ($this->defaultEntityNamespace !== NULL ? $this->defaultEntityNamespace . '\\' : '') . ucfirst(self::toCamelCase($table));
    }

    public static function toCamelCase($str)
    {
        return preg_replace_callback('#_(.)#', function ($m) {
            return strtoupper($m[1]);
        }, $str);
    }

    public function getColumn($entityClass, $field)
    {
        return self::toUnderScore($field);
    }

    public function getEntityField($table, $column)
    {
        return self::toCamelCase($column);
    }

    public function getTableByRepositoryClass($repositoryClass)
    {
        $matches = array();
        if (preg_match('#([a-z0-9]+)repository$#i', $repositoryClass, $matches)) {
            return self::toUnderScore($matches[1]);
        }
        throw new InvalidStateException('Cannot determine table name.');
    }

    public function getImplicitFilters($entityClass, Caller $caller = null)
    {
        $entityName = $this->trimNamespace($entityClass);
        if ($entityName === 'Team') {
            if ($caller->getComplement() instanceof Property && $caller->getComplement()->getName() === 'historyTeams') {
                return new ImplicitFilters(array('info', 'orderTeams'));
            }
            return new ImplicitFilters(array('info', 'actual', 'orderTeams'));
        } elseif (in_array($entityName, array('Category', 'Day', 'Photo'), TRUE)) {
            return new ImplicitFilters(array('year'));
        } elseif ($entityName === 'Match') {
            return new ImplicitFilters(array('orderMatches'));
        } elseif ($entityName === 'News') {
            return new ImplicitFilters(array('orderNews'));
        } elseif ($entityName === 'Photo') {
            return new ImplicitFilters(array('orderPhotos'));
        }
        return array();
    }


}
