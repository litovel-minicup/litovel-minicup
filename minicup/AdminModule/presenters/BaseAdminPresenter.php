<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Presenters\BasePresenter;
use Nette\Http\Response;

abstract class BaseAdminPresenter extends BasePresenter
{
    public function startup()
    {
        parent::startup();
        if (!$this->user->loggedIn) {
            $this->redirect(Response::S403_FORBIDDEN, ':Admin:Sign:in');
        }
    }

    protected function afterRender()
    {
        $this->redrawControl('flashes');
    }


}
