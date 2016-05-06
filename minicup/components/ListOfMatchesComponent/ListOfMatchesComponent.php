<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Day;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\Year;
use Minicup\Model\Repository\MatchRepository;
use Nette\InvalidArgumentException;
use Nette\Utils\DateTime;

interface IListOfMatchesComponentFactory
{
    /**
     * @param Day|Team|Category|Year|NULL $arg
     * @return ListOfMatchesComponent
     */
    public function create($arg);

}

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
     * @param MatchRepository             $MR
     */
    public function __construct($arg,
                                MatchRepository $MR)
    {
        parent::__construct();
        $this->arg = $arg;
        $this->MR = $MR;
    }

    public function render($mode = 'all', $limit = 0)
    {
        $matches = [];
        if ($this->arg instanceof Team) {
            $matches = $this->arg->i->matches;
            $this->template->team = $this->arg->i;
        } elseif ($this->arg instanceof Category) {
            if ($mode === 'current') {
                $matches = $this->MR->getCurrentMatches($this->arg, $limit);
            } elseif ($mode === 'next') {
                $matches = $this->MR->getNextMatches($this->arg, $limit);
            } elseif ($mode === 'last') {
                $matches = $this->MR->getLastMatches($this->arg, $limit);
            } elseif ($this->view === 'full' && $mode === 'all') {
                $this->template->days = $this->MR->groupMatchesByDay($this->arg);
            } elseif ($mode === 'all') {
                $matches = $this->arg->matches;
            } else {
                throw new InvalidArgumentException("Unknown render mode: '{$mode}'.");
            }
        }
        $this->template->matches = $matches;
        parent::render();
    }

    /**
     * @param int $time
     * @return bool
     */
    public function isToday($time)
    {
        $time = DateTime::from($time);
        $now = new DateTime();
        return $time->format('Y-m-d') === $now->format('Y-m-d');
    }
}

