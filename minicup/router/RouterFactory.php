<?php

namespace Minicup;

use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\CategoryRepository;
use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

class RouterFactory
{
    /** @var  CategoryRepository */
    private $CR;

    /**
     * @param CategoryRepository $CR
     */
    public function __construct(CategoryRepository $CR)
    {
        $this->CR = $CR;
    }

    /**
     * @return IRouter
     */
    public function createRouter()
    {
        $CR = $this->CR;
        $router = new RouteList();
        $router[] = new Route('category/<category>', array(
            'presenter' => 'Homepage',
            'action' => 'category',
            'module' => 'Front',
            'category' => array(
                Route::FILTER_IN => function ($slug) use ($CR) {
                    return $CR->getBySlug($slug);
                },
                Route::FILTER_OUT => function (Category $category) use ($CR) {
                    return $category->slug;
                }
            ),
        ));

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
