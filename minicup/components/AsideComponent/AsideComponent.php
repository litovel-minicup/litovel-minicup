<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\StaticContent;
use Minicup\Model\Manager\CacheManager;
use Minicup\Model\Manager\MatchManager;
use Minicup\Model\Repository\MatchRepository;
use Nette\Utils\DateTime;

interface IAsideComponentFactory
{
    /**
     * @param Category $category
     * @param string $tournamentStart
     * @return AsideComponent
     */
    public function create(Category $category, $tournamentStart);
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

    /** @var IStaticContentComponentFactory */
    private $ISCCF;

    /** @var CacheManager */
    private $cacheManager;

    private $tournamentStart;

    /**
     * @param Category $category
     * @param string $tournamentStart
     * @param IListOfMatchesComponentFactory $LOMCF
     * @param ICategoryTableComponentFactory $CTCF
     * @param ICountdownComponentFactory $CCF
     * @param MatchManager $MM
     * @param MatchRepository $MR
     * @param IStaticContentComponentFactory $ISCCF
     * @param CacheManager $cacheManager
     */
    public function __construct(Category $category,
                                $tournamentStart,
                                IListOfMatchesComponentFactory $LOMCF,
                                ICategoryTableComponentFactory $CTCF,
                                ICountdownComponentFactory $CCF,
                                MatchManager $MM,
                                MatchRepository $MR,
                                IStaticContentComponentFactory $ISCCF,
                                CacheManager $cacheManager)
    {
        $this->category = $category;
        $this->LOMCF = $LOMCF;
        $this->CCF = $CCF;
        $this->CTCF = $CTCF;
        $this->MM = $MM;
        $this->MR = $MR;
        $this->ISCCF = $ISCCF;
        $this->cacheManager = $cacheManager;
        $this->tournamentStart = $tournamentStart;
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
    protected function createComponentCategoryTableComponent(): CategoryTableComponent
    {
        return $this->CTCF->create($this->category);
    }

    /**
     * @return CountdownComponent
     */
    protected function createComponentCountdownComponent(): CountdownComponent
    {
        $firstMatch = $this->MR->getFirstMatchInCategory($this->category);
        $countdown = $firstMatch ? DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $firstMatch->matchTerm->day->day->format('Y-m-d') . ' ' . $firstMatch->matchTerm->start->format('H:i:s')
        ) : DateTime::createFromFormat('Y-m-d H:i:s', $this->tournamentStart);
        return $this->CCF->create($countdown);
    }


    /**
     * @return StaticContentComponent
     */
    protected function createComponentStreamComponent(): StaticContentComponent
    {
        $staticContentComponent = $this->ISCCF->create(StaticContent::STREAM, $this->category->year, FALSE);
        $staticContentComponent->onChange[] = function (StaticContent $content) {
            $this->cacheManager->cleanByEntity($this->category);
        };
        return $staticContentComponent;
    }

}