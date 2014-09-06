<?php

namespace Minicup\AdminModule\Presenters;

use Nette,
    Minicup\Model\Entity;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {
    public function renderDefault() {
        $e = new Entity\Match();
        \Tracy\Debugger::barDump($e);
    }
}
