<?php

namespace Minicup\Components;
use Minicup\Model\Entity\Category;

/**
 * Description of ICategoryTableFactory
 *
 * @author admin
 */
interface ICategoryTableComponentFactory
{
    /**
     * @param Category $category
     * @return CategoryTableComponent
     */
    public function create(Category $category);

}
