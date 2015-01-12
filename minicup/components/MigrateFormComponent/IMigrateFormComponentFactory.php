<?php

namespace Minicup\Components;


interface IMigrateFormComponentFactory
{
    /** @return MigrateFormComponent */
    public function create();

}