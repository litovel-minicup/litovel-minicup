<?php

namespace Minicup\Components;

/**
 * Description of ICategoryTableFactory
 *
 * @author admin
 */
interface ICategoryTableComponentFactory {
    /**
     * @return \Minicup\Components\CategoryTableComponent
     */
    public function create();
    
}
