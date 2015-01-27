<?php

namespace Minicup\Components;


use Minicup\Misc\EntitiesReplicatorContainer;
use Minicup\Model\Entity\Match;
use Minicup\Model\Repository\MatchRepository;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

class MatchFormComponent extends BaseComponent
{
    /** @var int */
    private $count;

    /** @var  MatchRepository */
    private $MR;

    public function __construct($count, MatchRepository $MR)
    {
        $this->count = $count;
        $this->MR = $MR;
    }

    public function render()
    {
        $this->template->match = $this->count;
        $this->template->render();
    }

    /**
     * @return Form
     */
    protected function createComponentMatchForm()
    {
        $me = $this;

        $f = $this->FF->create();



        /** @var EntitiesReplicatorContainer $matches */
        $matches = $f->addDynamic('matches', function (Container $container, Match $match) use ($me) {
            $container->currentGroup = $container->getForm()->addGroup('zápas', FALSE);
            $container->addText('scoreHome', 'skore home: '.$match->homeTeam->name);
            $container->addText('scoreAway', 'skore away: '.$match->awayTeam->name);
        }, $this->MR->findAll(), 1);


        /** @var SubmitButton $addSubmit */
        $addSubmit = $matches->addSubmit('addMatch', 'zobrazit další zápas')
            ->setValidationScope(FALSE)
            ->setAttribute('class', 'ajax')
            ->onClick[] = $this->addMatchClicked;
        $f->addSubmit('submit','odeslat');
        $f->onSuccess[] = $this->formSubmitted;
        return $f;
    }

    public function addMatchClicked(SubmitButton $button)
    {
        /** @var EntitiesReplicatorContainer $matches */
        $this->redrawControl('');
        $matches = $button->parent;
        $button->parent->createOne();
    }

    /***/
    public function formSubmitted(Form $form, ArrayHash $values)
    {
        Debugger::$maxDepth = 5;
        $this->template->data = $values;
    }


}