<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\AsideComponent;
use Minicup\Components\CategoryToggleComponent;
use Minicup\Components\IAsideComponentFactory;
use Minicup\Components\ICategoryToggleFormComponentFactory;
use Minicup\Presenters\BasePresenter;


/**
 * Base presenter for all application presenters.
 */
abstract class BaseFrontPresenter extends BasePresenter
{
    /** @var ICategoryToggleFormComponentFactory @inject */
    public $CTCF;

    /** @var IAsideComponentFactory @inject */
    public $ACF;

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->category = $this->category;
        $this->template->years = $this->YR->findArchiveYears();
        $this->template->actualYear = $this->YR->getActualYear();
        $this->template->categories = $this->YR->getSelectedYear()->categories;
    }

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

    protected function startup()
    {
        parent::startup();
        if ($this->category) {
            $this->getSession()->getSection('minicup')->offsetSet('category', $this->category->id);
        } else {
            $this->category = $this->CR->getDefaultCategory();
        }
    }
}
