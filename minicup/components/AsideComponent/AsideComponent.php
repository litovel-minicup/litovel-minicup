<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;
use Minicup\Model\Manager\MatchManager;
use Minicup\Model\Repository\MatchRepository;
use Nette\Utils\DateTime;

interface IAsideComponentFactory
{
    /**
     * @param Category $category
     * @return AsideComponent
     */
    public function create(Category $category);
}

/**
 * @property bool $playingTime
 * @property bool $started
 * @property bool $finished
 */
class AsideComponent extends BaseComponent
{
    /** @var ICategoryTableComponentFactory */
    private $CTCF;

    /** @var IListOfMatchesComponentFactory */
    private $LOMCF;

    /** @var ICountdownComponentFactory */
    private $CCF;

    /** @var Category */
    private $category;

    /** @var MatchManager */
    private $MM;

    /** @var MatchManager */
    private $MR;

    /**
     * @param Category                       $category
     * @param IListOfMatchesComponentFactory $LOMCF
     * @param ICategoryTableComponentFactory $CTCF
     * @param ICountdownComponentFactory     $CCF
     * @param MatchManager                   $MM
     * @param MatchRepository                $MR
     */
    public function __construct(Category $category,
                                IListOfMatchesComponentFactory $LOMCF,
                                ICategoryTableComponentFactory $CTCF,
                                ICountdownComponentFactory $CCF,
                                MatchManager $MM,
                                MatchRepository $MR)
    {
        $this->category = $category;
        $this->LOMCF = $LOMCF;
        $this->CCF = $CCF;
        $this->CTCF = $CTCF;
        $this->MM = $MM;
        $this->MR = $MR;
        parent::__construct();
    }

    public function render()
    {
        $this->template->category = $this->category;
        parent::render();
    }


    /**
     * @return bool
     */
    public function isPlayingTime()
    {
        return $this->MM->isPlayingTime($this->category);
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->MM->isStarted($this->category);
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return $this->MM->isFinished($this->category);
    }

    /**
     * @return ListOfMatchesComponent
     */
    protected function createComponentListOfMatchesAsideComponent()
    {
        return $this->LOMCF->create($this->category);
    }

    /**
     * @return CategoryTableComponent
     */
    protected function createComponentCategoryTableComponent()
    {
        return $this->CTCF->create($this->category);
    }

    /**
     * @return CountdownComponent
     */
    protected function createComponentCountdownComponent()
    {
        $firstMatch = $this->MR->getFirstMatchInCategory($this->category);
        $countdown = $firstMatch ? DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $firstMatch->matchTerm->day->day->format('Y-m-d') . ' ' . $firstMatch->matchTerm->start->format('H:i:s')
        ) : DateTime::createFromFormat('Y-m-d H:i:s', '2017-06-09 13:00:00');
        return $this->CCF->create($countdown);
    }

}