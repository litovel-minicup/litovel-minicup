<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IListOfNewsComponentFactory;
use Minicup\Components\IStaticContentComponentFactory;
use Minicup\Components\ListOfNewsComponent;
use Minicup\Components\StaticContentComponent;
use Minicup\Model\Manager\MatchManager;
use Minicup\Model\Repository\MatchRepository;
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
    /** @var MatchManager @inject */
    public $MM;
    /** @var MatchRepository @inject */
    public $MR;

    public function renderDefault()
    {
        foreach ($this->MR->findByIds([/*2843, 3098,3171, 3237 , 3303, 3369*/]) as $match) {
            $this->MM->regenerateFromMatch($match);
        }
    }

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
        return $this->SCCF->create($this->action, $this->category->year);
    }
}
