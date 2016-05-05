<?php

namespace Minicup\Presenters;

use Minicup\Components\ILoginFormComponentFactory;
use Minicup\Components\LoginFormComponent;
use Nette\Application\UI\Form;

class SignPresenter extends BasePresenter
{
    /** @var string @persistent */
    public $backlink;

    /** @var ILoginFormComponentFactory @inject */
    public $LFCF;

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
        $form->onSuccess[] = function () use ($presenter) {
            $presenter->restoreRequest($presenter->backlink);
            $presenter->redirect(':Admin:Homepage:default', ['category' => $this->category]);
        };
        return $loginForm;
    }

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

}
