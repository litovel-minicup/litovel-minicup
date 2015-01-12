<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Day;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\Year;
use Minicup\Model\Repository\MatchRepository;

class ListOfMatchesComponent extends BaseComponent
{
    /**
     * @var MatchRepository
     */
    private $MR;


    /**
     * @var Day|Year|Team|Category|NULL
     */
    private $arg;

    /**
     * @param Day|Year|Team|Category|NULL $arg
     * @param MatchRepository $MR
     */
    public function __construct($arg, MatchRepository $MR)
    {
        parent::__construct();
        $this->arg = $arg;
        $this->MR = $MR;
    }

    public function render()
    {
        $matches = [];
        $this->template->actualID = 0;
        if ($this->arg instanceof Team) {
            $matches = $this->arg->matches;
            $this->template->actualID = $this->arg->id;
        } elseif ($this->arg instanceof Category) {
            $matches = $this->arg->matches;
        }
        $this->template->matches = $matches;
        $this->template->render();

    }

    public function handleRefresh()
    {
        $this->redrawControl();
    }
}
