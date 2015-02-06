<?php

namespace Minicup\Misc;

use Nette\Object;
use Nextras\Application\LinkFactory;

class TexyFactory extends Object
{
    /** @var  LinkFactory */
    private $linkFactory;

    /** @var string */
    private $modulePrefix;

    /**
     * @param LinkFactory $linkFactory
     * @param string $modulePrefix
     */
    public function __construct($modulePrefix, LinkFactory $linkFactory)
    {
        $this->linkFactory = $linkFactory;
        $this->modulePrefix = $modulePrefix;
    }

    /**
     * @return Texy
     */
    public function create()
    {
        $t = new Texy($this->modulePrefix, $this->linkFactory);
        //TODO: configuration texy
        return $t;
    }
}