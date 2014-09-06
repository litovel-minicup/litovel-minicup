<?php

namespace Minicup\FrontModule\Presenters;

use Nette,
    Minicup\Model\Entity;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {
    public function renderDefault($param) {
        $e = new Entity\Match();
    }
}
