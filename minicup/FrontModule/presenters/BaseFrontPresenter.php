<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\AsideComponent;
use Minicup\Components\CategoryToggleFormComponent;
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
     * actual selected category
     *
     * @var Category
     * @persistent
     */
    public $category;

    /** @var ICategoryToggleFormComponentFactory @inject */
    public $CTCF;

    /** @var  IAsideComponentFactory @inject */
    public $ACF;

    /**
     * @return CategoryToggleFormComponent
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
        $section = $this->session->getSection('minicup');
        $this->category = $this->CR->getBySlug($section['category']);
    }

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->category = $this->category;
    }
}
