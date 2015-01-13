<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Day;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\Year;

interface IListOfMatchesComponentFactory
{
    /**
     * @param Day|Team|Category|Year|NULL $arg
     * @return ListOfMatchesComponent
     */
    public function create($arg);

}
