<?php

namespace Minicup\AdminModule\Presenters;

use Nette;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {
    public function startup() {
        parent::startup();
        if (!$this->user->loggedIn) {
            if ($this->user->logoutReason === \Nette\Security\User::INACTIVITY) {
                $this->flashMessage('Pro neaktivitu jste byl odhlášen, přihlašte se prosím.', 'info');
            } else {
                $this->flashMessage('Pro vstup do této sekce je nutné se přihlásit!', 'info');
            }
            $this->redirect(':Sign:in', array('backlink' => $this->storeRequest()));
        }
    }
}
