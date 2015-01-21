<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\CategoryToggleFormComponent;
use Minicup\Components\ICategoryToggleFormComponentFactory;
use Minicup\Model;
use Minicup\Presenters\BasePresenter;
use Nette;

/**
 * Base presenter for all application presenters.
 */
abstract class BaseFrontPresenter extends BasePresenter
{
    /** @var  ICategoryToggleFormComponentFactory @inject */
    public $CTCF;

    /**
     * @return CategoryToggleFormComponent
     */
    protected function createComponentCategoryToggleFormComponent()
    {
        return $this->CTCF->create();
    }
}
