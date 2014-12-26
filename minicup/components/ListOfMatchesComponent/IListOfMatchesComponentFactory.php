<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Day;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\Year;

interface IListOfMatchesComponentFactory
{
    /**
     * @param $controller Day|Team|Category|Year|NULL
     * @return ListOfMatchesComponent
     */
    public function create($controller);

}
