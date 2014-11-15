<?php

namespace Minicup\Presenters;

use Nette,
    Minicup\Model;

/**
 * Base presenter.
 */
class BasePresenter extends Nette\Application\UI\Presenter {

    /** @var \Minicup\Components\ILoginFormComponentFactory @inject */
    public $LFCF;

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentLoginForm() {
        return $this->LFCF->create();
    }

}
