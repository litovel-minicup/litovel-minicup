<?php

namespace Minicup\Components;


interface ICategoryToggleComponentFactory {
    /** @return CategoryToggleComponent */
    public function create();
}