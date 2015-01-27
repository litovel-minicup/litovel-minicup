<?php

namespace Minicup\Components;


interface IMatchFormComponentFactory
{
    /**
     * @param int $count
     * @return MatchFormComponent
     */
    public function create($count);
}