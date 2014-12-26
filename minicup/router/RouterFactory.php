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
     * @param TeamRepository $TR
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
        $categoryFilter = [
            Route::FILTER_IN => function ($slug) use ($CR) {
                return $CR->getBySlug($slug);
            },
            Route::FILTER_OUT => function (Category $category) use ($CR) {
                return $category->slug;
            }];

        $front = new RouteList('Front');

        $front[] = new Route('tymy[/<category>]', array(
            'presenter' => 'Team',
            'action' => 'default',
            'category' => $categoryFilter
        ));

        $front[] = new Route('zapasy[/<category>]', array(
            'presenter' => 'Match',
            'action' => 'default',
            'category' => $categoryFilter
        ));

        $front[] = new Route('<category>/<team>', array(
            'presenter' => 'Team',
            'action' => 'detail',
            'category' => $categoryFilter,
            'team' => [
                Route::FILTER_IN => function ($slug) use ($TR) {
                    return $TR->getBySlug($slug);
                },
                Route::FILTER_OUT => function (Team $team) use ($TR) {
                    return $team->slug;
                }]
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
