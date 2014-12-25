<?php

namespace Minicup\Presenters;

use Minicup\Components\ILoginFormComponentFactory;
use Minicup\Model;
use Minicup\ParamService;
use Nette;

/**
 * Base presenter.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /** @var ILoginFormComponentFactory @inject */
    public $LFCF;

    /** @var ParamService @inject */
    public $PS;

    /** @var Model\Repository\CategoryRepository @inject */
    public $CR;

    /** @var Model\Repository\YearRepository @inject */
    public $YR;

    /**
     * @return Nette\Application\UI\Form
     */
    protected function createComponentLoginForm()
    {
        return $this->LFCF->create();
    }

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->categories = $this->CR->findAll();
    }
}
