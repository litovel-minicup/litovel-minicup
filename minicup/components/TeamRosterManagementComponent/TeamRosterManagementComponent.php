<?php

namespace Minicup\Components;


use Minicup\Misc\EntitiesReplicatorContainer;
use Minicup\Model\Entity\Player;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Repository\PlayerRepository;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Manager\MatchManager;
use Minicup\Model\Repository\MatchRepository;
use Nette\Forms\Container;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

interface ITeamRosterManagementComponentFactory
{
    /**
     * @param TeamInfo $team
     * @return TeamRosterManagementComponent
     */
    public function create(TeamInfo $team);

}

/**
 * Class TeamRosterManagementComponent
 * @package Minicup\Components
 *
 * @brief
 */
class TeamRosterManagementComponent extends BaseComponent
{
    /** @var PlayerRepository */
    private $PR;

    /** @var TeamInfo */
    private $team;

    public function __construct(TeamInfo $team, PlayerRepository $PR)
    {
        parent::__construct();

        $this->team = $team;
        $this->PR = $PR;
    }


    /**
     * Render this component
     */
    public function render()
    {
        parent::render();
    }

    public function addPlayerClicked(SubmitButton $button)
    {
        /** @var EntitiesReplicatorContainer $container */
        $container = $button->parent;
        $container->createOne();
        $container->createOne();
        $this->redrawControl();
    }

    /**
     * @return Form
     */
    protected function createComponentRosterFormComponent()
    {
        $me = $this;

        $f = $this->formFactory->create();

        /** @var EntitiesReplicatorContainer $matches */
        $matches = $f->addDynamic('players', function (Container $container, $player) use ($me) {
            $container->setCurrentGroup($container->getForm()->addGroup('Hráč', FALSE));
            $id = $container->addHidden('id');
            $number = $container
                ->addText('number')
                ->setType('number');
            $name = $container
                ->addText('name');
            $surname = $container
                ->addText('surname');
            $secondaryNumber = $container
                ->addText('secondaryNumber')
                ->setType('number');
            $secondaryNumber->addCondition(Form::FILLED)->addRule(Form::INTEGER)->addRule(Form::RANGE, NULL, [1, 99]);

            if ($player) {
                /** @var Player $player */
                $player = $player;
                $container->setDefaults($player->getData());
            }
            $name->addConditionOn($number, Form::FILLED)->setRequired();
            $surname->addConditionOn($number, Form::FILLED)->setRequired();
            $number->addConditionOn($surname, Form::FILLED)->setRequired();
        }, $this->team->players, 12, TRUE);

        /** @var SubmitButton $addSubmit */
        $matches->addSubmit('addPlayer', 'přidat hráče')
            ->setValidationScope(FALSE)
            ->setAttribute('class', 'ajax')
            // ->addCreateOnClick(TRUE);
            ->onClick[] = [$this, 'addPlayerClicked'];

        $f->addSubmit('submit', 'uložit');

        $f->onSuccess[] = function (Form $form, ArrayHash $values) {
            /** @var SubmitButton $submitButton */
            $submitButton = $form['submit'];
            if ($submitButton->isSubmittedBy()) {
                foreach ($values['players'] as $playerId => $playerData) {
                    if (!$playerData['number']) {
                        continue;
                    }
                    Debugger::barDump($playerData);
                }
                $this->redirect('this');
            }
        };
        return $f;
    }


}