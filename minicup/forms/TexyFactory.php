<?php

namespace Minicup\Forms;


use Nette\Object;

class TexyFactory extends Object
{
    /**
     * @return \Texy
     */
    public function create()
    {
        $t = new \Texy();
        //TODO: configuration texy
        return $t;

    }
}