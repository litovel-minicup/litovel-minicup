<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Presenters\BasePresenter;

abstract class BaseAdminPresenter extends BasePresenter
{
    public function startup()
    {
        parent::startup();
        if (!$this->user->loggedIn) {
            $this->redirect(':Admin:Sign:in');
        }
    }

    protected function afterRender()
    {
        $this->redrawControl('flashes');
    }


}
