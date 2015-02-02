<?php

namespace Minicup\Presenters;

use Nette,
    Minicup\Model,
    Tracy\Debugger;

/**
 * Error presenter.
 */
class ErrorPresenter extends Nette\Application\UI\Presenter {

    /**
     * @param  Exception
     * @return void
     */
    public function renderDefault($exception) {
        if ($exception instanceof Nette\Application\BadRequestException) {
            $code = $exception->getCode();
            $this->setView(in_array($code, array(403, 404, 405, 410, 500)) ? $code : '4xx');
            Debugger::dump($code);
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
