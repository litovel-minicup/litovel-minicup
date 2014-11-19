<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Presenters\BasePresenter;
use Nette\Http\IResponse;
use Nette\Security\IUserStorage;
use Nette\Security\User;

/**
 * Base prosenter for adminModule
 */
abstract class BaseAdminPresenter extends BasePresenter {
    public function startup() {
        parent::startup();
        if (!$this->user->loggedIn) {
            if ($this->user->logoutReason === IUserStorage::INACTIVITY) {
                $this->error('Pro neaktivitu jste byl odhlášen, přihlašte se prosím.', IResponse::S403_FORBIDDEN);
            } else {
                $this->error('Pro vstup do této sekce je nutné se přihlásit!', IResponse::S403_FORBIDDEN);
            }
        }
    }
}
