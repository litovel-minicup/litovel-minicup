<?php

namespace Minicup\Components;


interface ICategoryToggleFormComponentFactory {
    /** @return CategoryToggleFormComponent */
    public function create();
}