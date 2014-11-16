<?php

namespace Minicup\Components;

use Minicup\Model\Repository\MatchRepository;
use Nette\Application\UI\Control;

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

    public function render()
    {
        $this->template->setFile(__DIR__ . '/ListOfMatchesComponent.latte');
        $this->template->matches = $this->MR->findAll();
        $this->template->render();

    }

    public function handleRefresh()
    {
        $this->redrawControl();
    }
}
