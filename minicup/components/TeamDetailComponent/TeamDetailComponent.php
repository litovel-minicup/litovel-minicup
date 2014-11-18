<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Team;
use Nette\Application\UI\Control;

class TeamDetailComponent extends Control
{

    /** @var Team */
    public $team;

    public function __construct(Team $team)
    {
        parent::__construct();
        $this->team = $team;
    }

    public function render(){
        
    }

}