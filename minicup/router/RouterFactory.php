<?php

namespace Minicup\Router;

use Minicup\Model\Entity\Tag;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\TeamInfo;
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

class RouterFactory extends Object {
    /** @var CategoryRepository */
    private $CR;

    /** @var TeamRepository */
    private $TR;

    /** @var YearRepository */
    private $YR;

    /** @var SessionSection */
    private $session;

    /** @var TagRepository */
    private $TagR;

    /**
     * @var YearCategoryRouteFactory
     */
    private $yearCategoryRouteFactory;

    /**
     * @param CategoryRepository       $CR
     * @param TeamRepository           $TR
     * @param YearRepository           $YR
     * @param TagRepository            $TagR
     * @param Session                  $session
     * @param YearCategoryRouteFactory $yearCategoryRouteFactory
     */
    public function __construct(CategoryRepository $CR,
                                TeamRepository $TR,
                                YearRepository $YR,
                                TagRepository $TagR,
                                Session $session,
                                YearCategoryRouteFactory $yearCategoryRouteFactory) {
        $this->CR = $CR;
        $this->TR = $TR;
        $this->YR = $YR;
        $this->TagR = $TagR;
        $this->session = $session->getSection('minicup');
        $this->yearCategoryRouteFactory = $yearCategoryRouteFactory;
    }

    /**
     * @return IRouter
     */
    public function create() {
        $CR = $this->CR;
        $YR = $this->YR;
        $TR = $this->TR;
        $TagR = $this->TagR;
        $session = $this->session;
        $route = $this->yearCategoryRouteFactory;
        $front = new RouteList('Front');

        $front[] = $route('foto/tagy[/<tags .+>]/', array(
            'presenter' => 'Gallery',
            'action' => 'tags',
            'tags' => array(
                Route::FILTER_IN => function ($tags) use ($TagR) {
                    $tags = Strings::split($tags, '#/#');
                    $tagsEntities = $TagR->findBySlugs($tags);
                    if (count($tagsEntities) !== count($tags)) {
                        return NULL;
                    }
                    return $tagsEntities;
                },
                Route::FILTER_OUT => function (array $tags) {
                    $tags = array_map(function (Tag $tag) {
                        return $tag->slug;
                    }, $tags);
                    sort($tags);
                    return implode('/', $tags);
                }
            )
        ));

        $front[] = $route('foto/detail/<tag>/', array(
            'presenter' => 'Gallery',
            'action' => 'detail',
            'tag' => array(
                Route::FILTER_IN => function ($tag) use ($TagR) {
                    /** @var Tag $tag */
                    $tag = $TagR->getBySlug($tag);
                    if (!$tag || !$tag->isMain) {
                        return NULL;
                    }
                    return $tag;
                },
                Route::FILTER_OUT => function ($tag) use ($TagR) {
                    if (is_string($tag)) {
                        $tag = $TagR->getBySlug($tag);
                        if (!$tag) {
                            return NULL;
                        }
                    }
                    if (!$tag->isMain) {
                        return NULL;
                    }
                    return $tag->slug;
                }
            )
        ));

        $front[] = $route('foto/', array(
            'presenter' => 'Gallery',
            'action' => 'default'
        ), 0, FALSE);

        $front[] = $route('foto/tagy/', array(
            'presenter' => 'Gallery',
            'action' => 'tags'
        ));

        $front[] = $route('zapasy/', array(
            'presenter' => 'Match',
            'action' => 'default'
        ));

        $front[] = $route('tymy/', array(
            'presenter' => 'Team',
            'action' => 'list'
        ));

        $front[] = $route('statistiky/', array(
            'presenter' => 'Stats',
            'action' => 'default'
        ));

        $front[] = $route('informace/', array(
            'presenter' => 'Homepage',
            'action' => 'informations'
        ));

        $front[] = $route('sponzori/', array(
            'presenter' => 'Homepage',
            'action' => 'sponsors'
        ));

        $front[] = $route('<team>/', array(
            'presenter' => 'Team',
            'action' => 'detail',
            NULL => array(
                Route::FILTER_IN => function ($params) use ($TR) {
                    if (!isset($params['team'], $params['category'])) {
                        return NULL;
                    }
                    $params['team'] = $TR->getBySlug($params['team'], $params['category']);
                    if (!$params['team']) {
                        return NULL;
                    }
                    return $params;
                },
                Route::FILTER_OUT => function ($params) use ($CR, $TR) {
                    if (!isset($params['team'], $params['category'])) {
                        return NULL;
                    }
                    $params['category'] = $CR->getBySlug($params['category']);
                    $team = $TR->getBySlug($params['team'], $params['category']);

                    if (!$team) {
                        return NULL;
                    }
                    $params['team'] = $team->slug;
                    return $params;
                }
            )
        ));

        $front[] = new Route('<category>/<team>', array(
            'presenter' => 'Team',
            'action' => 'detail',
            NULL => array(
                Route::FILTER_IN => function ($params) use ($TR, $CR, $YR) {
                    if (!isset($params['team'], $params['category'])) {
                        return NULL;
                    }
                    $params['category'] = $CR->getBySlug($params['category'], $CR->getBySlug('2014'));
                    $params['team'] = $TR->getBySlug($params['team'], $params['category']);
                    if (!$params['team']) {
                        return NULL;
                    }
                    return $params;
                }
            )
        ), Route::ONE_WAY);

        $router = new RouteList();

        $router[] = $front;

        $router[] = new Route('login/', 'Sign:in');
        $router[] = new Route('logout/', 'Sign:out');

        $router[] = $route('admin/<presenter>/<action>[/<id [0-9]*>]/', array(
            'module' => 'Admin',
            'presenter' => 'Homepage',
            'action' => 'default'
        ));

        $router[] = new Route('media/<action>/<slug>', array(
            'presenter' => 'Media',
        ));

        $router[] = $route('', array(
            'module' => 'Front',
            'presenter' => 'Homepage',
            'action' => 'default'
        ));

        // $front[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
        return $router;
    }

    /**
     * @param string  $teamSlug
     * @param Request $request
     * @return Team|NULL
     */
    public function teamSlug2Team($teamSlug, Request $request) {
        return $this->TR->getBySlug($teamSlug, $request->parameters['category']);
    }

    /**
     * @param TeamInfo|Team $team
     * @param Request       $request
     * @return string
     */
    public function team2TeamSlug($team, Request $request) {
        if ($team instanceof Team) {
            return $team->slug;
        } else {
            $category = $this->CR->getBySlug($request->parameters['category']);
            return $this->TR->getBySlug($team, $category)->i->slug;
        }
    }

}
