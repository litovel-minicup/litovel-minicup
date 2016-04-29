<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IListOfNewsComponentFactory;
use Minicup\Components\IStaticContentComponentFactory;
use Minicup\Components\ListOfNewsComponent;
use Minicup\Components\StaticContentComponent;
use Minicup\Model\Repository\StaticContentRepository;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseFrontPresenter
{
    /** @var IListOfNewsComponentFactory @inject */
    public $NLCF;

    /** @var IStaticContentComponentFactory @inject */
    public $SCCF;

    /** @var StaticContentRepository @inject */
    public $SCR;

    /**
     * @return ListOfNewsComponent
     */
    protected function createComponentNewsListComponent()
    {
        return $this->NLCF->create($this->category->year);
    }

    /**
     * @return StaticContentComponent
     */
    protected function createComponentStaticContentComponent()
    {
        return $this->SCCF->create($this->SCR->getBySlug($this->action));
    }
}
