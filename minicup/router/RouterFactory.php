<?php

namespace Minicup\Router;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Entity\Year;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\TagRepository;
use Minicup\Model\Repository\TeamRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Object;
use Nette\Utils\Strings;

class RouterFactory extends Object
{
    /** @var  CategoryRepository */
    private $CR;

    /** @var TeamRepository */
    private $TR;

    /** @var YearRepository */
    private $YR;

    /** @var  SessionSection */
    private $session;

    /** @var TagRepository */
    private $TagR;

    /**
     * @param CategoryRepository    $CR
     * @param TeamRepository        $TR
     * @param YearRepository        $YR
     * @param Session               $session
     */
    public function __construct(CategoryRepository $CR, TeamRepository $TR, YearRepository $YR, TagRepository $TagR, Session $session)
    {
        $this->CR = $CR;
        $this->TR = $TR;
        $this->YR = $YR;
        $this->TagR = $TagR;
        $this->session = $session->getSection('minicup');
    }

    /**
     * @return IRouter
     */
    public function create()
    {
        $CR = $this->CR;
        $YR = $this->YR;
        $TagR = $this->TagR;
        $session = $this->session;
        if (!isset($session['category'])) {
            $session['category'] = $CR->getDefaultCategory()->slug;
        }
        $categoryFilter = array(
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
        );

        $yearFilter = array(
            Route::FILTER_IN => function ($slug) use ($YR, $session) {
                $year = $YR->getBySlug($slug);
                if ($year) {
                    $session['year'] = $slug;
                    $YR->setSelectedYear($year);
                }
                return $year;
            },
            Route::FILTER_OUT => function (Year $year) {
                return $year->slug;
            },
            Route::VALUE => $YR->getSelectedYear()
        );

        $front = new RouteList('Front');

        /**  HOMEPAGE ROUTES */
        $front[] = new Route('', array(
            'presenter' => 'Homepage',
            'action' => 'default'
        ));

        $front[] = new Route('informace', array(
            'presenter' => 'Homepage',
            'action' => 'informations'
        ));

        $front[] = new Route('sponzori', array(
            'presenter' => 'Homepage',
            'action' => 'sponsors'
        ));

        $front[] = new Route('[<year>/]tymy', array(
            'presenter' => 'Team',
            'action' => 'default',
            'year' => $yearFilter
        ));

        $front[] = new Route('[<year>/]zapasy', array(
            'presenter' => 'Match',
            'action' => 'default',
            'year' => $yearFilter
        ));

        $front[] = new Route('[<year>/]tymy/<category>', array(
            'presenter' => 'Team',
            'action' => 'list',
            'year' => $yearFilter,
            'category' => $categoryFilter
        ));

        $front[] = new Route('[<year>/]zapasy/<category>', array(
            'presenter' => 'Match',
            'action' => 'list',
            'year' => $yearFilter,
            'category' => $categoryFilter
        ));

        $front[] = new Route('[<year>/]tabulky/<category>', array(
            'presenter' => 'Result',
            'action' => 'table',
            'year' => $yearFilter,
            'category' => $categoryFilter
        ));

        $front[] = new Route('[<year>/]foto', array(
            'presenter' => 'Gallery',
            'action' => 'default',
            'year' => $yearFilter
        ));

        $front[] = new Route('[<year>/]foto/tagy/<tags .+>', array(
            'presenter' => 'Gallery',
            'action' => 'tags',
            'year' => $yearFilter,
            'tags' => array(
                Route::FILTER_IN => function ($tags) use ($TagR) {
                    $tags = Strings::split($tags, '#/#');
                    $tagsEntities = $TagR->findBySlugs($tags);
                    if (count($tagsEntities) != count($tags)) {
                        return NULL;
                    }
                    return $tagsEntities;
                },
                Route::FILTER_OUT => function (array $tags) {
                    $tags = array_map(function (Tag $tag) { return $tag->slug; }, $tags);
                    sort($tags);
                    return join('/', $tags);
                }
            )
        ));

        $route = new FilterRoute('[<year>/]<category>/<team>', array(
            'presenter' => 'Team',
            'action' => 'detail',
            'year' => $yearFilter,
            'category' => $categoryFilter
        ));
        $route->addFilter('team', $this->teamSlug2Team, $this->team2TeamSlug);
        $front[] = $route;

        $router = new RouteList();
        $router[] = $front;
        $router[] = new Route('admin/<presenter>/<action>[/<category>]', array(
            'module' => 'Admin',
            'presenter' => 'Homepage',
            'action' => 'default',
            'category' => $categoryFilter
        ));

        $router[] = new Route('media/<action>/<slug>', array(
            'presenter' => 'Media',
        ));

        return $router;
    }

    /**
     * @param string    $teamSlug
     * @param Request   $request
     * @return Team|NULL
     */
    public function teamSlug2Team($teamSlug, Request $request)
    {
        return $this->TR->getBySlug($teamSlug, $request->parameters['category']);
    }

    /**
     * @param TeamInfo|Team     $team
     * @param Request           $request
     * @return string
     */
    public function team2TeamSlug($team, Request $request)
    {
        return $team->slug;
    }

}
