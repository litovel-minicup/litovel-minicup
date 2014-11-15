<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Presenters\BasePresenter;
use Nette\Security\User;

/**
 * Base prosenter for adminModule
 */
abstract class BaseAdminPresenter extends BasePresenter {
    public function startup() {
        parent::startup();
        if (!$this->user->loggedIn) {
            if ($this->user->logoutReason === User::INACTIVITY) {
                $this->flashMessage('Pro neaktivitu jste byl odhlášen, přihlašte se prosím.', 'info');
            } else {
                $this->flashMessage('Pro vstup do této sekce je nutné se přihlásit!', 'info');
            }
            $this->redirect(':Front:Homepage:', array('backlink' => $this->storeRequest()));
        }
    }
}
