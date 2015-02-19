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

class MatchFormComponent extends BaseComponent
{
    /** @var Category */
    private $category;

    /** @var int */
    private $count;

    /** @var MatchRepository */
    private $MR;

    /** @var MatchManager */
    private $MM;

    public function __construct(Category $category, $count, MatchRepository $MR, MatchManager $MM)
    {
        $this->category = $category;
        $this->count = $count;
        $this->MR = $MR;
        $this->MM = $MM;
    }

    public function render()
    {
        $this->template->match = $this->category;
        $this->template->render();
    }

    /**
     * @return Form
     */
    protected function createComponentMatchForm()
    {
        $me = $this;

        $f = $this->formFactory->create();

        /** @var EntitiesReplicatorContainer $matches */
        $matches = $f->addDynamic('matches', function (Container $container, Match $match) use ($me) {
            $container->currentGroup = $container->getForm()->addGroup('zápas', FALSE);
            $container
                ->addText('scoreHome', $match->homeTeam->name)
                ->setType('number')
                ->addCondition(Form::INTEGER);
            $container
                ->addText('scoreAway', $match->awayTeam->name)
                ->setType('number')
                ->addCondition(Form::INTEGER);
        },
            $this->MR->findMatchesByCategory($this->category, MatchRepository::UNCONFIRMED),
            $this->count);

        /** @var SubmitButton $addSubmit */
        $addSubmit = $matches->addSubmit('addMatch', 'zobrazit další zápas')
            ->setValidationScope(FALSE)
            ->setAttribute('class', 'ajax')
            ->onClick[] = $this->addMatchClicked;
        $f->addSubmit('submit', 'odeslat');
        $f->onSuccess[] = $this->formSubmitted;
        return $f;
    }

    public function addMatchClicked(SubmitButton $button)
    {
        /** @var EntitiesReplicatorContainer $matches */
        $this->redrawControl('');
        $button->parent->createOne();
    }

    /***/
    public function formSubmitted(Form $form, ArrayHash $values)
    {
        /** @var SubmitButton $submitButton */
        $submitButton = $form['submit'];
        if ($submitButton->isSubmittedBy()) {
            foreach ($values['matches'] as $matchId => $matchData) {
                // TODO: add Nette validation
                if (!$matchData['scoreHome'] || !$matchData['scoreAway']) {
                    continue;
                }
                /** @var Match $match */
                $match = $this->MR->get((int)$matchId);
                $this->MM->confirmMatch($match, $matchData['scoreHome'], $matchData['scoreAway']);
                $this->presenter->flashMessage('Zápas '.$match->homeTeam->name.' vs. '.$match->awayTeam->name.' byl úspěšně zpracován.');
            }

        }
        $this->redirect('this');
    }


}