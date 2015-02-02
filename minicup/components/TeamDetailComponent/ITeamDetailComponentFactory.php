<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Team;

/**
 * ITeamDetailComponentFactory
 */
interface ITeamDetailComponentFactory
{
    /**
     * @param $team Team
     * @return TeamDetailComponent
     */
    public function create(Team $team);
}