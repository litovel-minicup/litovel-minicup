<?php

namespace Minicup\Presenters;

use Minicup\Components\ILoginFormComponentFactory;
use Minicup\Components\LoginFormComponent;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

class SignPresenter extends BasePresenter
{

    /** @var ILoginFormComponentFactory @inject */
    public $LFCF;

    public function actionOut()
    {
        $this->user->logout(TRUE);
        $this->redirect(':Front:Homepage:');
    }

    public function formatTemplateFiles()
    {
        $dir = $this->context->parameters['appDir'];
        return ["$dir/templates/{$this->name}.{$this->action}.latte"];
    }

    protected function startup()
    {
        parent::startup();
        $this->module = 'admin';
    }

    /**
     * @return LoginFormComponent
     */
    protected function createComponentLoginFormComponent()
    {
        $presenter = $this;
        /** @var LoginFormComponent $loginForm */
        $loginForm = $this->LFCF->create();
        /** @var Form $form */
        $form = $loginForm['loginForm'];
        $form->addHidden('backlink', $this->getParameter('_backlink'));
        $form->onSuccess[] = function (Form $form, ArrayHash $values) use ($presenter) {
            $backlink = $values->offsetGet('backlink');
            // $presenter->restoreRequest($backlink);
            $presenter->redirect(':Admin:Homepage:default', ['category' => $this->category]);
        };
        return $loginForm;
    }

}
