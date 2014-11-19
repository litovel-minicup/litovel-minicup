<?php

namespace Minicup\Components;

use Minicup\Model\Repository\MatchRepository;
use Nette\Application\UI\Control;
use Nette\Utils\DateTime;

/**
 *
 */
class ListOfMatchesComponent extends Control
{
    /**
     *
     * @var \Minicup\Model\Repository\MatchRepository
     */
    private $MR;

    public function __construct(MatchRepository $MR)
    {
        parent::__construct();
        $this->MR = $MR;
    }

    public function render(DateTime $datetime)
    {
        $this->template->setFile(__DIR__ . '/ListOfMatchesComponent.latte');
        $this->template->matches = $this->MR->findMatchesByDate($datetime);
        $this->template->date = $datetime;
        $this->template->render();

    }

    public function handleRefresh()
    {
        $this->redrawControl();
    }
}
