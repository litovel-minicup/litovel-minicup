<?php

namespace Minicup\FrontModule\Presenters;
use Minicup\Components\INewsListComponentFactory;
use Minicup\Components\IStaticContentComponentFactory;
use Minicup\Components\NewsListComponent;
use Minicup\Model\Repository\StaticContentRepository;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseFrontPresenter
{
    /** @var INewsListComponentFactory @inject */
    public $NLCF;

    /** @var IStaticContentComponentFactory @inject */
    public $SCCF;

    /** @var StaticContentRepository @inject */
    public $SCR;

    /**
     * @return NewsListComponent
     */
    protected function createComponentNewsListComponent()
    {
    	return $this->NLCF->create();
    }

    protected function createComponentStaticContentComponent()
    {
        return $this->SCCF->create($this->SCR->getBySlug($this->action));
    }


}
