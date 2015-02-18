<?php

namespace Minicup\Misc;

use Nette\Application\LinkGenerator;
use Nette\Object;

class TexyFactory extends Object
{
    /** @var  LinkGenerator */
    private $linkGenerator;

    /** @var string */
    private $modulePrefix;

    /**
     * @param LinkFactory $linkGenerator
     * @param string $modulePrefix
     */
    public function __construct($modulePrefix, LinkGenerator $linkGenerator)
    {
        $this->linkGenerator = $linkGenerator;
        $this->modulePrefix = $modulePrefix;
    }

    /**
     * @return Texy
     */
    public function create()
    {
        $t = new Texy($this->modulePrefix, $this->linkGenerator);
        //TODO: custom texy configuration
        return $t;
    }
}