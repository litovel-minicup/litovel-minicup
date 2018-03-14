<?php

namespace Minicup\Components;


interface ITeamRosterAdministrationComponent
{
    /**
     * @return TeamRosterAdministrationComponent
     */
    public function create();

}

/**
 * Class TeamRosterAdministrationComponent
 * @package Minicup\Components
 *
 * @brief
 */
class TeamRosterAdministrationComponent extends BaseComponent
{

    /**
     * Render this component
     */
    public function render()
    {
        parent::render();
    }


}