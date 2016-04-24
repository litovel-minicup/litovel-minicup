<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\OnlineReport;
use Minicup\Model\Repository\OnlineReportRepository;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

class OnlineReportComponent extends BaseComponent
{

    /** @var Match */
    private $match;

    /** @var OnlineReportRepository */
    private $ORR;

    /**
     * @param Match                  $match
     * @param OnlineReportRepository $ORR
     */
    public function __construct(Match $match, OnlineReportRepository $ORR)
    {
        parent::__construct();
        $this->match = $match;
        $this->ORR = $ORR;
    }

    public function render()
    {
        $this->template->time = time();
        $this->template->match = $this->match;
        parent::render();
    }

    public function handleRefresh()
    {
        $this->redrawControl('reports');
        $this->redrawControl('heading');
    }

    /**
     * @return Form
     */
    public function createComponentNewReportForm()
    {
        $form = $this->formFactory->create();
        $form->addText('message', '', 50)
            ->setRequired('Zprávu prostě musíš vyplnit!');
        $form->addSubmit('info', 'INFO');
        $form->addSubmit('action', 'AKCE');
        $form->addSubmit('goal', 'GÓL');
        $form->addSubmit('penalty', 'VYLOUČENÍ');
        $form->elementPrototype->class = 'ajax';
        $form->onSuccess[] = $this->newReportFormSubmitted;
        return $form;
    }

    /**
     * @param Form      $form
     * @param ArrayHash $values
     */
    public function newReportFormSubmitted($form, $values)
    {
        if (!$this->presenter->user->isAllowed('online-report', 'write')) {
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

interface IOnlineReportComponentFactory
{
    /**
     * @param $match Match
     * @return OnlineReportComponent
     */
    public function create(Match $match);

}
