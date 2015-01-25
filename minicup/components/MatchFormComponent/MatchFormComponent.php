<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Match;
use Nette\Application\UI\Form;

class MatchFormComponent extends BaseComponent
{
    /** @var Match */
    private $match;

    public function __construct(Match $match)
    {
        $this->match = $match;
    }

    public function render()
    {
        $this->template->match = $this->match;
        $this->template->render();
    }

    /**
     * @return Form
     */
    protected function createComponentMatchForm()
    {
        $f = $this->FF->create();
        $f->addText('scoreHome')->setValue($this->match->scoreHome);
        $f->addText('scoreAway')->setValue($this->match->scoreAway);
        $f->addHidden('matchId', $this->match->id);
        return $f;
    }

}