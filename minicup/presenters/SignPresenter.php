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
        /** @var LoginFormComponent $loginFormComponent */
        $loginFormComponent = $this->LFCF->create(function (Form $form, ArrayHash $values) use ($presenter) {
            $presenter->redirect(':Admin:Homepage:default', ['category' => $this->category]);
        });

        return $loginFormComponent;
    }

}
