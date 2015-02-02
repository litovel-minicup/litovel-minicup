<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Category;

interface IListOfTeamsComponentFactory
{
    /**
     * @param $category
     * @return ListOfTeamsComponent
     */
    public function create(Category $category);

}
