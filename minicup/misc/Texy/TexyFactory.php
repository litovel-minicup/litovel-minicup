<?php

namespace Minicup\Misc;

use Nette\Application\LinkGenerator;
use Nette\Object;

class TexyFactory extends Object {
    /** @var  LinkGenerator */
    private $linkGenerator;

    /** @var string */
    private $modulePrefix;

    /**
     * @param string        $modulePrefix
     * @param LinkGenerator $linkGenerator
     */
    public function __construct($modulePrefix, LinkGenerator $linkGenerator) {
        $this->linkGenerator = $linkGenerator;
        $this->modulePrefix = $modulePrefix;
    }

    /**
     * @return Texy
     */
    public function create() {
        $t = new Texy($this->modulePrefix, $this->linkGenerator);
        //TODO: custom texy configuration
        $t->setOutputMode(Texy::HTML5);
        return $t;
    }
}