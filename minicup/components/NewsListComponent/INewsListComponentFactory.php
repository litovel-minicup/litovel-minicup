<?php

namespace Minicup\Components;


interface INewsListComponentFactory
{
    /**
     * @return NewsListComponent
     */
    public function create();

}