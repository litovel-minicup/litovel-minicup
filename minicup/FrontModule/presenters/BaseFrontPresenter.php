<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IListOfMatchesComponentFactory;
use Minicup\Presenters\BasePresenter;
use Nette,
    Minicup\Model;

/**
 * Base presenter for all application presenters.
 */
abstract class BaseFrontPresenter extends BasePresenter {
    /**
     * @var IListOfMatchesComponentFactory @inject
     */
    public $LOFCFactory;

    public function createComponentListOfMatchesComponent()
    {
        return $this->LOFCFactory->create();
    }

}
