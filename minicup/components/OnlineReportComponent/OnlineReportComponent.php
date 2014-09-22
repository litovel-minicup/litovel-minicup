<?php

namespace Minicup\Components;

use \Nette\Application\UI\Control,
    \Nette\Application\UI\Form,
    Minicup\Model\Entity\OnlineReport,
    Minicup\Model\Repository\OnlineReportRepository;

class OnlineReportComponent extends Control {

    /** @var \Minicup\Model\Entity\Match */
    public $match;

    /** @var \Minicup\Model\Repository\OnlineReportRepository */
    private $ORR;

    public function __construct(OnlineReportRepository $ORR) {
        parent::__construct();
        $this->ORR = $ORR;
    }
    public function render() {
        $this->template->setFile(__DIR__ . '/OnlineReportComponent.latte');
        $this->template->time = time();
        $this->template->match = $this->match;
        $this->template->render();
    }
    public function handleRefresh() {
        $this->redrawControl();
    }
    public function createComponentNewReportForm() {
        $form = new Form();
        $form->addText('message', '', 50)
                ->setRequired('Zprávu prostě musíš vyplnit!');
        $form->addSubmit('info', 'INFO');
        $form->addSubmit('action', 'AKCE');
        $form->addSubmit('goal', 'GÓL');
        $form->addSubmit('penalty', 'VYLOUČENÍ');
        $form->getElementPrototype()->class = 'ajax';
        $form->onSuccess[] = $this->newReportFormSubmitted;
        return $form;
    }
    /**
     * @param Form $form
     * @param \Nette\Utils\ArrayHash $values
     */
    public function newReportFormSubmitted($form, $values) {
        if (!$this->presenter->user->isAllowed('online', 'write')) {
            $this->presenter->flashMessage('Pro tuto akci nejste oprávněn!', 'error');
            $this->presenter->redirect('Front:Homepage:default');
        }
        $ORE = new OnlineReport();
        $ORE->assign($values);
        $ORE->added = new \DateTime();
        $ORE->match = $this->match;
        $ORE->type = $form->submitted->name;
        $this->ORR->persist($ORE);
        $this->redrawControl();
    }

}
