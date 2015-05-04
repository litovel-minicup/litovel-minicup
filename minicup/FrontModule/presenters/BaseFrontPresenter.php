<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\AsideComponent;
use Minicup\Components\CategoryToggleComponent;
use Minicup\Components\IAsideComponentFactory;
use Minicup\Components\ICategoryToggleFormComponentFactory;
use Minicup\Model\Entity\Category;
use Minicup\Presenters\BasePresenter;


/**
 * Base presenter for all application presenters.
 */
abstract class BaseFrontPresenter extends BasePresenter
{
    /**
     * @var Category
     * @persistent
     */
    public $category;

    /** @var ICategoryToggleFormComponentFactory @inject */
    public $CTCF;

    /** @var IAsideComponentFactory @inject */
    public $ACF;

    /**
     * @return CategoryToggleComponent
     */
    protected function createComponentCategoryToggleFormComponent()
    {
        return $this->CTCF->create();
    }

    /**
     * @return AsideComponent
     */
    protected function createComponentAsideComponent()
    {
        return $this->ACF->create($this->category);
    }

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->category = $this->category;
    }
}
