<?php

namespace Minicup\Components;


interface IAsideComponentFactory
{
    /** @return AsideComponent */
    public function create();
}