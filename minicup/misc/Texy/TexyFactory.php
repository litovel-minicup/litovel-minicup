<?php

namespace Minicup\Misc;

use Nette\Object;
use Nextras\Application\LinkFactory;

class TexyFactory extends Object
{
    /** @var  LinkFactory */
    private $linkFactory;

    /**
     * @param LinkFactory $linkFactory
     */
    public function __construct(LinkFactory $linkFactory)
    {
        $this->linkFactory = $linkFactory;
    }

    /**
     * @return Texy
     */
    public function create()
    {
        $t = new Texy('Front:', $this->linkFactory);
        //TODO: configuration texy
        return $t;
    }
}