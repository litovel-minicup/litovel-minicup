<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;

interface IAsideComponentFactory
{
    /**
     * @param Category $category
     * @return AsideComponent
     */
    public function create(Category $category);
}