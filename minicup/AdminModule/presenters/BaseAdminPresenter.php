<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Presenters\BasePresenter;
use Nette\Security\IUserStorage;

abstract class BaseAdminPresenter extends BasePresenter
{
    public function startup()
    {
        parent::startup();
        if (!$this->user->loggedIn) {
            if ($this->user->logoutReason === IUserStorage::INACTIVITY) {
                $message = 'Pro neaktivitu jste byl odhlášen, přihlašte se prosím.';
            } else {
                $message = 'Pro vstup do této sekce je nutné se přihlásit!';
            }
            $this->flashMessage($message, 'error');
            $this->redirect(':Front:Homepage:default', array('backlink' => $this->storeRequest()));
        }
    }

    protected function afterRender()
    {
        $this->redrawControl('flashes');
    }


}
