<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;

interface IMatchFormComponentFactory
{
    /**
     * @param Category $category
     * @param int $count
     * @return MatchFormComponent
     */
    public function create(Category $category, $count);
}