<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Category;

interface ICategoryTableComponentFactory
{
    /**
     * @param Category $category
     * @return CategoryTableComponent
     */
    public function create(Category $category);

}

class CategoryTableComponent extends BaseComponent
{

    /** @var Category */
    private $category;

    public function __construct(Category $category)
    {
        parent::__construct();
        $this->category = $category;
    }

    public function render()
    {
        $this->template->teams = $this->category->teams;
        parent::render();
    }
}
