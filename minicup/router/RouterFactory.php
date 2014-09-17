<?php

namespace Minicup;

use Nette\Application\Routers\RouteList,
    Nette\Application\Routers\Route;

/**
 * Router factory.
 */
class RouterFactory {

    /**
     * @return \Nette\Application\IRouter
     */
    public function createRouter() {
        $router = new RouteList();
        $router[] = new Route('login[/<action>]', 'Sign:in');
        
        $router[] = new Route('admin/<presenter>/<action>[/<id>]', array(
            'module' => 'admin',
            'presenter' => 'Homepage',
            'action' => 'default'
        ));

        $router[] = new Route('<presenter>/<action>[/<id>]', array(
            'module' => 'front',
            'presenter' => 'Homepage',
            'action' => 'default'
        ));

        return $router;
    }

}
