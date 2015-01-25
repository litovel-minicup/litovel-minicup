<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Match;

interface IMatchFormComponentFactory
{
    /**
     * @param Match $match
     * @return MatchFormComponent
     */
    public function create(Match $match);
}