<?php

namespace Minicup\Components;

use \Nette\Application\UI\Control,
    \Nette\Application\UI\Form,
    Minicup\Model\Entity\OnlineReport,
    Minicup\Model\Repository\OnlineReportRepository;

class OnlineReportComponent extends Control {

    /** @var \Minicup\Model\Entity\Match */
    public $match;
    
    /** @persistent @var int */
    public $match_id;

    /** @var \Minicup\Model\Repository\OnlineReportRepository */
    public $ORR;

    public function __construct(OnlineReportRepository $ORR) {
        parent::__construct();
        $this->ORR = $ORR;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/OnlineReportComponent.latte');
        $this->template->time = time();
        $this->template->match = $this->match;
        $this->match_id = $this->match->id;
        $this->template->render();
    }

    public function handleRefresh() {
        $this->invalidateControl();
    }

    public function createComponentNewReportForm() {
        $form = new Form();
        $form->addText('message', '', 50);
        $form->addSubmit('add', 'Přidat');
        $form->getElementPrototype()->class = 'ajax';
        $form->onSuccess[] = $this->newReportFormSubmitted;
        return $form;
    }

    public function newReportFormSubmitted($form, $values) {
        if (!$this->presenter->user->isAllowed('online', 'write')) {
            $this->presenter->flashMessage('Pro tuto akci nejste oprávněn!', 'error');
            $this->presenter->redirect('Front:Homepage:default');
        }
        $ORE = new OnlineReport();
        $ORE->assign($values, ['message']);
        $ORE->datetime = new \DateTime();
        $ORE->match = $this->match;
        $this->ORR->persist($ORE);
        $this->invalidateControl();
    }

}
