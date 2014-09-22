<?php

namespace Minicup\Components;

/**
 * ILoginFormComponentFactory
 */
interface ILoginFormComponentFactory {
    /**
     * @return \Minicup\Components\LoginFormComponent
     */
    public function create();
    
}
