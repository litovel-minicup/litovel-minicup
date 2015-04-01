<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;
use Minicup\Model\Manager\MatchManager;

class AsideComponent extends BaseComponent
{
    /** @var ICategoryTableComponentFactory */
    private $CTCF;

    /** @var IListOfMatchesComponentFactory */
    private $LOMCF;

    /** @var Category */
    private $category;

    /** @var MatchManager */
    private $MM;

    /**
     * @param Category $category
     * @param IListOfMatchesComponentFactory $LOMCF
     * @param ICategoryTableComponentFactory $CTCF
     * @param MatchManager $MM
     */
    public function __construct(Category $category, IListOfMatchesComponentFactory $LOMCF, ICategoryTableComponentFactory $CTCF, MatchManager $MM)
    {
        $this->category = $category;
        $this->LOMCF = $LOMCF;
        $this->CTCF = $CTCF;
        $this->MM = $MM;
    }

    /**
     * @return bool
     */
    public function isPlayingTime()
    {
        return $this->MM->isPlayingTime($this->category);
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

}

interface IAsideComponentFactory
{
    /**
     * @param Category $category
     * @return AsideComponent
     */
    public function create(Category $category);
}