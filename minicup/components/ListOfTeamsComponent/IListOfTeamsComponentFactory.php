<?php

namespace Minicup\Components;

interface IListOfTeamsComponentFactory
{
    /**
     * @return \Minicup\Components\ListOfTeamsComponent
     */
    public function create();

}
