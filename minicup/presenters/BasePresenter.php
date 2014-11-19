<?php

namespace Minicup\Presenters;

use Minicup\Model;
use Minicup\ParamService;
use Nette;

/**
 * Base presenter.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /** @var \Minicup\Components\ILoginFormComponentFactory @inject */
    public $LFCF;

    /** @var ParamService @inject */
    public $PS;

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentLoginForm()
    {
        return $this->LFCF->create();
    }

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->days = $this->PS['days'];
    }
}
