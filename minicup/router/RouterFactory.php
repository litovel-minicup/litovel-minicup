<?php

namespace Minicup\Router;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\TeamRepository;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Object;

class RouterFactory extends Object
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
        $front[] = new Route('', 'Homepage:default');
        $front[] = new Route('migrate', 'Homepage:migrate');
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

        $front[] = (new FilterRoute('<category>/<team>', array(
            'presenter' => 'Team',
            'action' => 'detail',
            'category' => $categoryFilter
        )))->addFilter('team', $this->teamSlug2Team, $this->team2TeamSlug);


        $router = new RouteList();
        $router[] = $front;
        $router[] = new Route('admin/<presenter>/<action>[/<id>]', array(
            'module' => 'admin',
            'presenter' => 'Homepage',
            'action' => 'default'
        ));

        return $router;
    }

    /**
     * @param $teamSlug string
     * @param Request $request
     * @return Team|NULL
     */
    public function teamSlug2Team($teamSlug, Request $request)
    {
        return $this->TR->getBySlug($teamSlug, $request->parameters['category']);
    }

    /**
     * @param Team $team
     * @param Request $request
     * @return string
     */
    public function team2TeamSlug(Team $team, Request $request)
    {
        return $team->slug;
    }

}
