<?php

namespace Minicup\Router;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\TeamRepository;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Object;

class RouterFactory extends Object
{
    /** @var  CategoryRepository */
    private $CR;

    /** @var  TeamRepository */
    private $TR;

    /** @var  SessionSection */
    private $session;

    /**
     * @param CategoryRepository $CR
     * @param TeamRepository $TR
     * @param Session $session
     */
    public function __construct(CategoryRepository $CR, TeamRepository $TR, Session $session)
    {
        $this->CR = $CR;
        $this->TR = $TR;
        $this->session = $session->getSection('minicup');
    }

    /**
     * @return IRouter
     */
    public function create()
    {
        $CR = $this->CR;
        $session = $this->session;
        if (!isset($session['category'])) {
            $session['category'] = $CR->getDefaultCategory()->slug;
        }
        $categoryFilter = [
            Route::FILTER_IN => function ($slug) use ($CR, $session) {
                $category = $CR->getBySlug($slug);
                if ($category) {
                    $session['category'] = $slug;
                }
                return $category;
            },
            Route::FILTER_OUT => function (Category $category) use ($CR) {
                return $category->slug;
            }
        ];

        $front = new RouteList('Front');
        $front[] = new Route('', 'Homepage:default');
        $front[] = new Route('tymy', 'Team:default');
        $front[] = new Route('zapasy', 'Match:default');

        $front[] = new Route('tymy/<category>', [
            'presenter' => 'Team',
            'action' => 'list',
            'category' => $categoryFilter
        ]);

        $front[] = new Route('zapasy/<category>', [
            'presenter' => 'Match',
            'action' => 'list',
            'category' => $categoryFilter
        ]);

        $front[] = (new FilterRoute('<category>/<team>', [
            'presenter' => 'Team',
            'action' => 'detail',
            'category' => $categoryFilter
        ]))->addFilter('team', $this->teamSlug2Team, $this->team2TeamSlug);


        $router = new RouteList();
        $router[] = $front;
        $router[] = new Route('admin/<presenter>/<action>[/<id>]', [
            'module' => 'admin',
            'presenter' => 'Homepage',
            'action' => 'default'
        ]);

        return $router;
    }

    /**
     * @param $teamSlug
     * @param Request $request
     * @return Team|NULL
     */
    public function teamSlug2Team($teamSlug, Request $request)
    {
        return $this->TR->getBySlug($teamSlug, $request->parameters['category']);
    }

    /**
     * @param TeamInfo|Team $team
     * @param Request $request
     * @return string
     */
    public function team2TeamSlug($team, Request $request)
    {
        return $team->slug;
    }

}
