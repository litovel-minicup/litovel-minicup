<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\AsideComponent;
use Minicup\Components\CategoryToggleComponent;
use Minicup\Components\IAsideComponentFactory;
use Minicup\Components\ICategoryToggleComponentFactory;
use Minicup\Components\IYearToggleComponentFactory;
use Minicup\Components\YearToggleComponent;
use Minicup\Presenters\BasePresenter;
use Nette\Http\Url;


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

    /** @var Url */
    public $liveServiceUrl;

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->category = $this->category;
        $this->template->years = $this->YR->findArchiveYears();
        $this->template->actualYear = $this->YR->getActualYear();
        $this->template->categories = $this->YR->getSelectedYear()->categories;
        $this->template->liveServiceUrl = $this->liveServiceUrl;
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
        return $this->ACF->create($this->category, $this->context->getParameters()['tournamentStart']);
    }
}
