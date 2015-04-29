<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Presenters\BasePresenter;
use Nette\Application\ForbiddenRequestException;

abstract class BaseAdminPresenter extends BasePresenter
{
    public function startup()
    {
        parent::startup();
        if (!$this->user->loggedIn) {
            throw new ForbiddenRequestException();
        }
    }

    protected function afterRender()
    {
        $this->redrawControl('flashes');
    }


}
