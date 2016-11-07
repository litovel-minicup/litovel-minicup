<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\AsideComponent;
use Minicup\Components\CategoryToggleComponent;
use Minicup\Components\IAsideComponentFactory;
use Minicup\Components\ICategoryToggleComponentFactory;
use Minicup\Components\IYearToggleComponentFactory;
use Minicup\Components\YearToggleComponent;
use Minicup\Presenters\BasePresenter;


/**
 * Base presenter for all application presenters.
 */
abstract class BaseFrontPresenter extends BasePresenter
{
    /** @var ICategoryToggleComponentFactory @inject */
    public $CTCF;

    /** @var IYearToggleComponentFactory @inject */
    public $YTCF;

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
    protected function createComponentCategoryToggleComponent()
    {
        return $this->CTCF->create();
    }

    /**
     * @return YearToggleComponent
     */
    protected function createComponentActualYearToggleComponent()
    {
        return $this->YTCF->create($this->category);
    }

    /**
     * @return YearToggleComponent
     */
    protected function createComponentArchiveYearsToggleComponent()
    {
        return $this->YTCF->create($this->category);
    }

    /**
     * @return AsideComponent
     */
    protected function createComponentAsideComponent()
    {
        return $this->ACF->create($this->category);
    }
}
