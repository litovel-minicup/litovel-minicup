<?php

namespace Minicup\Components;

/**
 * ILoginFormComponentFactory
 */
interface ILoginFormComponentFactory
{
    /**
     * @return LoginFormComponent
     */
    public function create();

}
