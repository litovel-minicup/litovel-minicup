<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Day;
use Minicup\Model\Repository\MatchRepository;
use Nette\Application\UI\Control;
use Nette\Utils\DateTime;

class ListOfMatchesComponent extends Control
{
    /**
     *
     * @var MatchRepository
     */
    private $MR;

    public function __construct(MatchRepository $MR)
    {
        parent::__construct();
        $this->MR = $MR;
    }

    public function render(Day $day)
    {
        $this->template->setFile(__DIR__ . '/ListOfMatchesComponent.latte');
        $matches = [];
        foreach ($day->matchTerms as $mt) {
            foreach ($mt->matches as $match) {
                $matches[] = $match;
            }
        }
        $this->template->matches = $matches;
        $this->template->day = $day;
        $this->template->render();

    }

    public function handleRefresh()
    {
        $this->redrawControl();
    }
}
