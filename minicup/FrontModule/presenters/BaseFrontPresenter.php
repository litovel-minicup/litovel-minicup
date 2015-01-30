<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\CategoryToggleFormComponent;
use Minicup\Components\ICategoryTableComponentFactory;
use Minicup\Components\ICategoryToggleFormComponentFactory;
use Minicup\Components\IListOfMatchesComponentFactory;
use Minicup\Components\ListOfMatchesComponent;
use Minicup\Components\ListOfTeamsComponent;
use Minicup\Model\Entity\Category;
use Minicup\Presenters\BasePresenter;
use Nette\Http\Session;


/**
 * Base presenter for all application presenters.
 */
abstract class BaseFrontPresenter extends BasePresenter
{
    /** @var Category @persistent */
    public $category;

    /** @var Session @inject */
    public $session;

    /** @var ICategoryToggleFormComponentFactory @inject */
    public $CTCF;

    /** @var IListOfMatchesComponentFactory @inject */
    public $LOMCF;

    /** @var ICategoryTableComponentFactory @inject */
    public $CTF;

    /**
     * @return CategoryToggleFormComponent
     */
    protected function createComponentCategoryToggleFormComponent()
    {
        return $this->CTCF->create();
    }

    /**
     * @return ListOfMatchesComponent
     */
    protected function createComponentListOfMatchesComponent()
    {
        return $this->LOMCF->create($this->category);
    }

    /**
     * @return ListOfTeamsComponent
     */
    protected function createComponentCategoryTableComponent()
    {
        return $this->CTF->create($this->category);
    }

    protected function startup()
    {
        parent::startup();
        $this->category = $this->CR->getBySlug($this->session->getSection('minicup')['category']);
    }

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->category = $this->category;
    }
}
