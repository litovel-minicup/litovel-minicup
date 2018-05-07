<?php

namespace Minicup\Components;


use Dibi\DateTime;
use Minicup\Misc\EntitiesReplicatorContainer;
use Minicup\Model\Connection;
use Minicup\Model\Entity\Player;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Repository\PlayerRepository;
use Minicup\Model\Repository\TeamInfoRepository;
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
    /** @var TeamInfoRepository */
    private $TIR;

    /** @var TeamInfo */
    private $team;

    public function __construct(TeamInfo $team, PlayerRepository $PR, TeamInfoRepository $TIR)
    {
        parent::__construct();
        $this->team = $team;
        $this->PR = $PR;
        $this->TIR = $TIR;
    }

    public function render()
    {
        $this->template->team = $this->team;
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

        $f->addText('trainerName')->setRequired('Jméno vedoucího/trenéra je povinná položka.');
        $f->addText('dressColor')->setRequired('Barva dresů je povinná položka.');
        $f->addText('dressColorSecondary');
        $f->setDefaults($this->team->getData(['trainerName', 'dressColor', 'dressColorSecondary']));

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
            $number
                ->addCondition(Form::FILLED)
                ->addRule(Form::INTEGER, 'Zadejte číslo jako numerickou hodnotu.')
                ->addRule(Form::RANGE, 'Číslo na dresu je možné mít od %d do %d.', [1, 99]);
            $secondaryNumber
                ->addCondition(Form::FILLED)
                ->addRule(Form::INTEGER, 'Zadejte číslo jako numerickou hodnotu.')
                ->addRule(Form::RANGE, 'Číslo na dresu je možné mít od %d do %d.', [1, 99]);

            if ($player) {
                /** @var Player $player */
                $player = $player;
                $container->setDefaults($player->getData());
            }
            $name->addConditionOn($number, Form::FILLED)->setRequired('Pole jméno je povinné.');
            $surname->addConditionOn($number, Form::FILLED)->setRequired('Pole příjmení je povinné.');
        }, $this->team->players, 16);

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
                /** @var Player[] $players */
                $players = [];
                $knownNumbers = [];
                $knownSecNumbers = [];
                foreach ($values['players'] as $playerId => $playerData) {
                    if (!$playerData['number']) {
                        continue;
                    }
                    $p = new Player();
                    $p->teamInfo = $this->team;
                    // dump($playerData);
                    // dump($knownNumbers);
                    $p->assign($playerData, ['name', 'number', 'surname', 'secondaryNumber']);
                    Debugger::barDump($p->secondaryNumber);
                    if (in_array($p->number, $knownNumbers, TRUE)) {
                        $form->addError("Duplicitní číslo hráče {$p->number}.");
                    }

                    if (in_array($p->secondaryNumber, $knownSecNumbers, TRUE)) {
                        $form->addError("Duplicitní druhé číslo hráče {$p->secondaryNumber}.");
                    }
                    $knownNumbers[] = $p->number;
                    if ($p->secondaryNumber)
                        $knownSecNumbers[] = $p->secondaryNumber;
                    $players[] = $p;
                }
                Debugger::barDump($form->getErrors());
                if ($form->hasErrors()) return;

                foreach ($this->team->players as $player) {
                    $this->PR->delete($player);
                }
                foreach ($players as $player) {
                    $this->PR->persist($player);
                }

                $this->team->assign($values, ['trainerName', 'dressColor', 'dressColorSecondary']);
                $this->team->updated = new DateTime();
                $this->TIR->persist($this->team);
                $count = count($players);
                $this->getPresenter()->flashMessage("Informace včetně všech {$count} hráčů byly úspěšně uloženy.");
                $this->redirect('this');
            }
        };
        return $f;
    }


}