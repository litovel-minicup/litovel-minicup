<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Presenters\BasePresenter;

abstract class BaseAdminPresenter extends BasePresenter
{
    public function startup()
    {
        parent::startup();
        if (!$this->user->loggedIn) {
            $this->flashMessage('Pro vstup do administrace je nutné se přihlásit.', 'error');
            $this->redirect(":Sign:in", array('backlink' => $this->storeRequest()));
        }
    }

    protected function afterRender()
    {
        $this->redrawControl('flashes');
    }


}
