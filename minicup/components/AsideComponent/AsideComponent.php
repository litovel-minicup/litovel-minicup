<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;

class AsideComponent extends BaseComponent
{
    /** @var ICategoryTableComponentFactory */
    private $CTCF;

    /** @var IListOfMatchesComponentFactory */
    private $LOMCF;

    /** @var Category */
    private $category;

    public function __construct(Category $category, IListOfMatchesComponentFactory $LOMCF, ICategoryTableComponentFactory $CTCF)
    {
        $this->category = $category;
        $this->LOMCF = $LOMCF;
        $this->CTCF = $CTCF;
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