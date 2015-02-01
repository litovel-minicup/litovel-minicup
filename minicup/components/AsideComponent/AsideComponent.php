<?php

namespace Minicup\Components;


class AsideComponent extends BaseComponent
{
    /** @var ICategoryTableComponentFactory */
    private $CTCF;

    /** @var IListOfMatchesComponentFactory */
    private $LOMCF;

    public function __construct(IListOfMatchesComponentFactory $LOMCF, ICategoryToggleFormComponentFactory $CTCF)
    {
        $this->LOMF = $LOMCF;
        $this->CTCF = $CTCF;
    }


}