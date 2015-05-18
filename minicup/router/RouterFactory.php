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
     * @param CategoryRepository $CR
     * @param TeamRepository $TR
     * @param YearRepository $YR
     * @param TagRepository $TagR
     * @param Session $session
     */
    public function __construct(CategoryRepository $CR,
                                TeamRepository $TR,
                                YearRepository $YR,
                                TagRepository $TagR,
                                Session $session)
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
        if (isset($this->session['category'])) {
            $category = $CR->getBySlug($this->session['category']);
        } else {
            $category = $CR->getDefaultCategory();
        }
        $categoryFilter = array(
            Route::FILTER_IN => function ($slug) use ($CR, $session) {
                $category = $CR->getBySlug($slug);
                if ($category) {
                    $session['category'] = $category->slug;
                    return $category;
                }
                return NULL;
            },
            Route::FILTER_OUT => function ($category) use ($CR) {
                if ($category instanceof Category) {
                    return $category->slug;
                } else {
                    return $CR->getBySlug($category)->slug;
                }
            }
        );

        $categoryAdvFilter = array_merge($categoryFilter, array(Route::VALUE => $category));

        // TODO: think about custom year selecting
        $yearFilter = array(
            Route::FILTER_IN => function ($slug) use ($YR) {
                $year = $YR->getBySlug($slug);
                if ($year) {
                    return $YR->setSelectedYear($year);
                }
                return NULL;
            },
            Route::FILTER_OUT => function (Year $year) {
                return $year->slug;
            },
            Route::VALUE => $YR->getSelectedYear()
        );

        $front = new RouteList('Front');

        /**  HOMEPAGE ROUTES */
        $front[] = new Route('informace/<category>', array(
            'presenter' => 'Homepage',
            'action' => 'informations',
            'category' => $categoryAdvFilter
        ));

        $front[] = new Route('sponzori/<category>', array(
            'presenter' => 'Homepage',
            'action' => 'sponsors',
            'category' => $categoryAdvFilter,
        ));

        $front[] = new Route('zapasy/<category>', array(
            'presenter' => 'Match',
            'action' => 'default',
            'category' => $categoryFilter
        ));

        $front[] = new Route('tymy/<category>', array(
            'presenter' => 'Team',
            'action' => 'list',
            'category' => $categoryFilter,
        ));

        $front[] = new Route('zapasy/<category>', array(
            'presenter' => 'Match',
            'action' => 'list',
            'category' => $categoryFilter,
        ));

        $front[] = new Route('statistiky/<category>', array(
            'presenter' => 'Stats',
            'action' => 'default',
            'category' => $categoryFilter,
        ));

        $front[] = new Route('foto/tagy/[<category>/][/<tags .+>]', array(
            'presenter' => 'Gallery',
            'action' => 'tags',
            'category' => $categoryAdvFilter,
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

        $front[] = new Route('foto/[<category>/]detail/<tag>', array(
            'presenter' => 'Gallery',
            'action' => 'detail',
            'category' => $categoryAdvFilter,
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

        $front[] = new Route('foto/<category>', array(
            'presenter' => 'Gallery',
            'action' => 'default',
            'category' => $categoryAdvFilter,
        ));

        $front[] = new Route('<category>/', array(
            'presenter' => 'Homepage',
            'action' => 'default',
            'category' => $categoryAdvFilter,
        ));

        $route = new FilterRoute('<category>/<team>', array(
            'presenter' => 'Team',
            'action' => 'detail',
            'category' => $categoryFilter
        ));
        $route->addFilter('team', $this->teamSlug2Team, $this->team2TeamSlug);
        $front[] = $route;

        $router = new RouteList();
        $router[] = new Route('admin/<presenter>/<action>[/<id [0-9]*>][/<category>]', array(
            'module' => 'Admin',
            'presenter' => 'Homepage',
            'action' => 'default',
            'category' => $categoryFilter
        ));

        $router[] = new Route('media/<action>/<slug>', array(
            'presenter' => 'Media',
        ));

        $router[] = new Route('login/', "Sign:in");
        $router[] = new Route('logout/', "Sign:out");

        $router[] = $front;
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
        if ($team instanceof Team) {
            return $team->slug;
        } else {
            $category = $this->CR->getBySlug($request->parameters['category']);
            return $this->TR->getBySlug($team, $category)->i->slug;
        }
    }

}
