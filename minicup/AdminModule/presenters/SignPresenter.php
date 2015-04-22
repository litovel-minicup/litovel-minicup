<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Components\ILoginFormComponentFactory;
use Minicup\Components\LoginFormComponent;
use Minicup\Presenters\BasePresenter;

class SignPresenter extends BasePresenter
{
    /** @var ILoginFormComponentFactory @inject */
    public $LFCF;

    protected function startup()
    {
        parent::startup();
        if ($this->user->loggedIn && $this->action !== "out") {
            $this->redirect(":Admin:Homepage:");
        }
    }

    /**
     * @return LoginFormComponent
     */
    protected function createComponentLoginFormComponent()
    {
        return $this->LFCF->create();
    }

    public function actionOut()
    {
        $this->user->logout(TRUE);
        $this->redirect(':Front:Homepage:');
    }
}
