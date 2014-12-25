<?php

namespace Minicup;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\TeamRepository;
use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

class RouterFactory
{
    /** @var  CategoryRepository */
    private $CR;

    /** @var  TeamRepository */
    private $TR;

    /**
     * @param CategoryRepository $CR
     */
    public function __construct(CategoryRepository $CR, TeamRepository $TR)
    {
        $this->CR = $CR;
        $this->TR = $TR;
    }

    /**
     * @return IRouter
     */
    public function createRouter()
    {
        $CR = $this->CR;
        $TR = $this->TR;
        $category = array(
            Route::FILTER_IN => function ($slug) use ($CR) {
                return $CR->getBySlug($slug);
            },
            Route::FILTER_OUT => function (Category $category) use ($CR) {
                return $category->slug;
            });

        $front = new RouteList('Front');

        $front[] = new Route('tymy[/<category>]', array(
            'presenter' => 'Team',
            'action' => 'default',
            'category' => $category
        ));

        $front[] = new Route('<category>/<team>', array(
            'presenter' => 'Team',
            'action' => 'detail',
            'category' => $category,
            'team' => array(
                Route::FILTER_IN => function ($slug) use ($TR) {
                    return $TR->getBySlug($slug);
                },
                Route::FILTER_OUT => function (Team $team) use ($TR) {
                    return $team->slug;
                })
        ));

        $router = new RouteList();
        $router[] = $front;
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
