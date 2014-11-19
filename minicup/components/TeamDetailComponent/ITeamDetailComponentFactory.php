<?php

namespace Minicup\Components;
use Minicup\Model\Entity\Team;

/**
 * ITeamDetailComponentFactory
 */

interface ITeamDetailComponentFactory{
    /**
     * @param $team Team
     * @return \Minicup\Components\TeamDetailComponent
     */
    public function create($team);
}