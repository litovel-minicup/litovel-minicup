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

    public function render($mode = 'all', $limit = 0)
    {
        $matches = array();
        $this->template->actualID = 0;
        if ($this->arg instanceof Team) {
            $matches = $this->arg->i->matches;
            $this->template->actualID = $this->arg->id;
        } elseif ($this->arg instanceof Category) {
            if ($mode == 'current') {
                $matches = $this->MR->getCurrentMatches($this->arg, $limit);
            } elseif ($mode == 'next') {
                $matches = $this->MR->getNextMatches($this->arg, $limit);
            } else {
                $matches = $this->arg->matches;
            }

        }
        $this->template->matches = $matches;
        parent::render();

    }
}

interface IListOfMatchesComponentFactory
{
    /**
     * @param Day|Team|Category|Year|NULL $arg
     * @return ListOfMatchesComponent
     */
    public function create($arg);

}

