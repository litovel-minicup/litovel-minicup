<?php

namespace Minicup\Components;


use Minicup\Misc\EntitiesReplicatorContainer;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Manager\MatchManager;
use Minicup\Model\Repository\MatchRepository;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;

interface IMatchDetailComponentFactory
{
    /**
     * @param Match $match
     * @return MatchDetailComponent
     */
    public function create(Match $match);
}

class MatchDetailComponent extends BaseComponent
{
    /** @var Match */
    private $match;

    public function __construct(Match $match)
    {
        $this->match = $match;
        parent::__construct();
    }

    public function render()
    {
        $this->template->match = $this->match;
        parent::render();
    }

}