<?php

namespace Minicup;


use Nette\NotSupportedException;
use Nette\Object;
use Nette\Utils\DateTime;

/** @deprecated */
class ParamService extends Object implements \ArrayAccess
{
    /** @var array */
    private $params = Array();

    public function __construct($params)
    {
        $days = isset($params['days']) ? $params['days'] : [];
        $params['days'] = array_map(function ($val) {
            return new DateTime($val);
        }, $days);
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function offsetExists($offset)
    {
        return isset($this->params[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->params[$offset]);
    }

    public function offsetGet($offset)
    {
        return NULL;
        if ($this->offsetExists($offset)) {
            return $this->params[$offset];
        } else {
            throw new \OutOfRangeException("Key $offset not exists.");
        }
    }

    public function offsetSet($offset, $val)
    {
        throw new NotSupportedException('Do it in config.neon!');
    }
} 