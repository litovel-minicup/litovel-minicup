<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Day;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\Year;
use Minicup\Model\Repository\MatchRepository;
use Nette\Application\UI\Control;

class ListOfMatchesComponent extends Control
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
        if ($this->arg instanceof Team) {
            $matches = $this->MR->findMatchesToTeam($this->arg);
            $matches = $this->arg->matches;
        }
        $this->template->setFile(__DIR__ . '/ListOfMatchesComponent.latte');
        $this->template->matches = $matches;
        $this->template->render();

    }

    public function handleRefresh()
    {
        $this->redrawControl();
    }
}
