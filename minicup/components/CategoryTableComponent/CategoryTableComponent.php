<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Category;

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
