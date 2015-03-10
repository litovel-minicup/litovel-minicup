<?php

namespace Minicup\FrontModule\Presenters;
use Minicup\Components\INewsListComponentFactory;
use Minicup\Components\NewsListComponent;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseFrontPresenter
{
    /** @var INewsListComponentFactory @inject */
    public $NLCF;

    /**
     * @return NewsListComponent
     */
    protected function createComponentNewsListComponent()
    {
    	return $this->NLCF->create();
    }

}
