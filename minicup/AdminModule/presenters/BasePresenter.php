<?php

namespace Minicup\AdminModule\Presenters;

use Nette\Security\User,
    Nette\Application\UI\Presenter;

/**
 * Base prosenter for adminModule
 */
abstract class BasePresenter extends Presenter {
    public function startup() {
        parent::startup();
        if (!$this->user->loggedIn) {
            if ($this->user->logoutReason === User::INACTIVITY) {
                $this->flashMessage('Pro neaktivitu jste byl odhlášen, přihlašte se prosím.', 'info');
            } else {
                $this->flashMessage('Pro vstup do této sekce je nutné se přihlásit!', 'info');
            }
            $this->redirect(':Sign:in', array('backlink' => $this->storeRequest()));
        }
    }
}
