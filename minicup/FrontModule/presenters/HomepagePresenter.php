<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IMatchFormComponentFactory;
use Minicup\Components\IStaticContentComponentFactory;
use Minicup\Components\StaticContentComponent;
use Minicup\Model\Repository\StaticContentRepository;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseFrontPresenter
{
    /** @var IStaticContentComponentFactory @inject */
    public $SCCF;

    /** @var StaticContentRepository @inject */
    public $SCR;

    /**
     * @return StaticContentComponent
     */
    protected function createComponentStaticContent()
    {
        return $this->SCCF->create($this->SCR->get(1));
    }

    /** @var IMatchFormComponentFactory @inject */
    public $MFCF;

}
