<?php

namespace Minicup\Presenters;

use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
use Tracy\Debugger;

class ErrorPresenter extends Presenter
{
    /**
     * @param  Exception
     * @return void
     */
    public function renderDefault($exception)
    {
        if ($exception instanceof BadRequestException) {
            $code = $exception->getCode();
            $this->setView(in_array($code, array(403, 404, 405, 410, 500), TRUE) ? $code : '4xx');
            Debugger::log("HTTP code $code: {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}", 'access');
        } else {
            $this->setView('500');
            Debugger::log($exception, Debugger::EXCEPTION);
        }
        if ($this->isAjax()) {
            $this->payload->error = TRUE;
            $this->terminate();
        }
    }

}
