<?php

namespace Minicup\Router;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\TagRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Minicup\Model\Repository\TeamRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\SmartObject;
use Nette\Utils\Strings;

class RouterFactory
{

    use SmartObject;
    /** @var CategoryRepository */
    private $CR;

    /** @var TeamRepository */
    private $TR;

    /** @var TeamInfoRepository */
    private $TIR;

    /** @var YearRepository */
    private $YR;

    /** @var SessionSection */
    private $session;

    /** @var TagRepository */
    private $TagR;

    /** @var MatchRepository */
    private $MR;

    /** @var YearCategoryRouteFactory */
    private $yearCategoryRouteFactory;

    /**
     * @param CategoryRepository       $CR
     * @param TeamRepository           $TR
     * @param YearRepository           $YR
     * @param TeamInfoRepository       $TIR
     * @param TagRepository            $TagR
     * @param MatchRepository          $MR
     * @param Session                  $session
     * @param YearCategoryRouteFactory $yearCategoryRouteFactory
     */
    public function __construct(CategoryRepository $CR,
                                TeamRepository $TR,
                                YearRepository $YR,
                                TeamInfoRepository $TIR,
                                TagRepository $TagR,
                                MatchRepository $MR,
                                Session $session,
                                YearCategoryRouteFactory $yearCategoryRouteFactory)
    {
        $this->CR = $CR;
        $this->TR = $TR;
        $this->YR = $YR;
        $this->TIR = $TIR;
        $this->TagR = $TagR;
        $this->MR = $MR;
        $this->session = $session->getSection('minicup');
        $this->yearCategoryRouteFactory = $yearCategoryRouteFactory;
    }

    /**
     * @return IRouter
     */
    public function create()
    {
        $CR = $this->CR;
        $YR = $this->YR;
        $TR = $this->TR;
        $TagR = $this->TagR;
        $route = $this->yearCategoryRouteFactory;
        $front = new RouteList('Front');

        $front[] = $route('foto/tagy[/<tags .+>]/', [
            'presenter' => 'Gallery',
            'action' => 'tags',
            'tags' => [
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
            ]
        ]);

        $front[] = $route('foto/detail/<tag>/', [
            'presenter' => 'Gallery',
            'action' => 'detail',
            'tag' => [
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
            ],
            NULL => [
                Route::FILTER_IN => function ($params) use ($TagR) {
                    /** @var Tag $tag */
                    $tag = $TagR->getBySlug($params['tag'], $params['category']->year);
                    if (!$tag || !$tag->isMain) {
                        return NULL;
                    }
                    $params['tag'] = $tag;
                    return $params;
                }
            ]
        ]);

        $front[] = $route('foto/', [
            'presenter' => 'Gallery',
            'action' => 'default'
        ], 0, FALSE);

        $front[] = $route('foto/prezentace/', [
            'presenter' => 'Gallery',
            'action' => 'presentation'
        ]);

        $front[] = $route('foto/tagy/', [
            'presenter' => 'Gallery',
            'action' => 'tags'
        ]);

        $front[] = $route('zapasy/', [
            'presenter' => 'Match',
            'action' => 'default'
        ]);

        $front[] = $route('zive/', [
            'presenter' => 'Match',
            'action' => 'online'
        ]);

        $front[] = $route(Match::MATCH_DETAIL_BASE_URL_PATTERN, [
            'presenter' => 'Match',
            'action' => 'detail',
            NULL => [
                Route::FILTER_OUT => function (array $params) {
                    if (!isset($params['match']))
                        return NULL;
                    if (!($params['match'] instanceof Match))
                        return NULL;
                    $params['match'] = sprintf(
                        Match::MATCH_DETAIL_URL_PART_PATTERN,
                        $params['match']->homeTeam->slug,
                        $params['match']->awayTeam->slug
                    );
                    return $params;
                },
                Route::FILTER_IN => function ($params) {
                    $slug = $params['match'];
                    $parts = Strings::split($slug, Match::MATCH_DETAIL_URL_PART_SPLITTER);
                    if (\count($parts) !== 2) return NULL;
                    [$home, $away] = $parts;

                    // get teams from slugs
                    $home = $this->TIR->getBySlug($params['category'], $home);
                    $away = $this->TIR->getBySlug($params['category'], $away);
                    if (!$home || !$away) return NULL;

                    $match = $this->MR->getCommonMatchForTeams($home->team, $away->team, NULL);
                    if (!$match) return NULL;

                    $params['match'] = $match;
                    return $params;
                },
            ]
        ]);

        $front[] = $route('tymy/', [
            'presenter' => 'Team',
            'action' => 'list'
        ]);

        $front[] = $route('statistiky/', [
            'presenter' => 'Stats',
            'action' => 'default'
        ]);

        $front[] = $route('informace/', [
            'presenter' => 'Homepage',
            'action' => 'informations'
        ]);

        $front[] = $route('sponzori/', [
            'presenter' => 'Homepage',
            'action' => 'sponsors'
        ]);

        $front[] = $route('<team>/', [
            'presenter' => 'Team',
            'action' => 'detail',
            NULL => [
                Route::FILTER_IN => function ($params) use ($TR) {
                    if (!isset($params['team'], $params['category'])) {
                        return NULL;
                    }
                    $team = $TR->getBySlug($params['team'], $params['category']);
                    if (!$team) {
                        return NULL;
                    }
                    $params['team'] = $team->i;
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
                    $params['team'] = $team->i->slug;
                    return $params;
                }
            ]
        ]);

        $front[] = new Route('<category>/<team>', [
            'presenter' => 'Team',
            'action' => 'detail',
            NULL => [
                Route::FILTER_IN => function ($params) use ($TR, $CR, $YR) {
                    if (!isset($params['team'], $params['category'])) {
                        return NULL;
                    }
                    $params['category'] = $CR->getBySlug($params['category'], $CR->getBySlug('2014'));
                    if ($params['category'] === NULL) {
                        return NULL;
                    }
                    $team = $TR->getBySlug($params['team'], $params['category']);
                    if (!$team) {
                        return NULL;
                    }
                    $params['team'] = $team->i;
                    return $params;
                }
            ]
        ], Route::ONE_WAY);

        $router = new RouteList();

        $router[] = $front;

        $router[] = new Route('login/', 'Sign:in');
        $router[] = new Route('logout/', 'Sign:out');

        $router[] = new Route('admin/<presenter>/<action>/[<category>][/<id [0-9]*>]/', [
            'module' => 'Admin',
            'presenter' => 'Homepage',
            'action' => 'default',
            'category' => $route->getCategoryMetadata(FALSE)
        ]);

        $router[] = new Route('admin/<presenter>/<action>/<category>[/<id [0-9]*>]/', [
            'module' => 'Admin',
            'presenter' => 'Homepage',
            'action' => 'default',
            'category' => $route->getCategoryMetadata(TRUE)
        ]);

        $api = $this->createApiRouter();
        $management = $this->createManagementRouter();

        $router[] = $management;
        $router[] = $api;

        $router[] = new Route('media/<action>/<slug>', [
            'presenter' => 'Media',
        ]);

        $router[] = $route->route('', [
            'module' => 'Front',
            'presenter' => 'Homepage',
            'action' => 'default'
        ], 0, TRUE);

        $category = $this->CR->get($this->session->offsetGet('category'), FALSE) ?: $this->CR->getDefaultCategory();
        $router[] = new Route('', [
            'module' => 'Front',
            'presenter' => 'Homepage',
            'action' => 'default',
            $route::DEFAULT_CATEGORY_KEY => $route->getCategoryMetadata(TRUE) + [Route::VALUE => $category],
        ], Route::ONE_WAY);

        // $front[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
        return $router;
    }

    protected function createApiRouter()
    {
        $list = new RouteList('Api');

        $matchFilter = [
            Route::FILTER_IN => function ($id) {
                return $this->MR->get($id);
            },
            Route::FILTER_OUT => function (Match $match) {
                return $match->id;
            }
        ];

        $categoryFilter = [
            Route::FILTER_IN => function ($id) {
                return $this->CR->get($id, FALSE);
            },
            Route::FILTER_OUT => function (Category $category) {
                return $category->id;
            }
        ];

        $list[] = new Route('api/v1/news-live-bar/<category>', [
            'presenter' => 'News',
            'action' => 'liveBar',
            'category' => $categoryFilter,
        ]);

        $list[] = new Route('api/v1/match/detail/<match>', [
            'presenter' => 'Match',
            'action' => 'detail',
            'match' => $matchFilter,
        ]);

        $list[] = new Route('api/v1/match/events/<match>', [
            'presenter' => 'Match',
            'action' => 'events',
            'match' => $matchFilter,
        ]);

        $list[] = new Route('api/v1/category/upcoming-matches/<category>', [
            'presenter' => 'Category',
            'action' => 'upcomingMatches',
            'category' => $categoryFilter,
        ]);

        $list[] = new Route('api/v1/instagram-stories', [
            'presenter' => 'Instagram',
            'action' => 'stories',
        ]);

        $list[] = new Route('api/v1/main-tags', [
            'presenter' => 'Tag',
            'action' => 'mainTags',
        ]);

        return $list;
    }

    /**
     * @return array|RouteList
     */
    protected function createManagementRouter()
    {
        $managementTeamFilter = [
            Route::FILTER_IN => function ($token) {
                return $this->TIR->getByToken($token);
            },
            Route::FILTER_OUT => function (TeamInfo $team) {
                return $team->authToken;
            }
        ];

        $management = new RouteList('Management');

        $management[] = new Route('management/<team>/soupiska', [
            'presenter' => 'TeamRoster',
            'action' => 'default',
            'team' => $managementTeamFilter
        ]);

        $management[] = new Route('management/<team>', [
            'presenter' => 'Homepage',
            'action' => 'default',
            'team' => $managementTeamFilter
        ]);
        return $management;
    }

    /**
     * @param string  $teamSlug
     * @param Request $request
     * @return Team|NULL
     */
    public function teamSlug2Team($teamSlug, Request $request)
    {
        return $this->TR->getBySlug($teamSlug, $request->parameters['category']);
    }

    /**
     * @param TeamInfo|Team $team
     * @param Request       $request
     * @return string
     */
    public function team2TeamSlug($team, Request $request)
    {
        if ($team instanceof Team) {
            return $team->i->slug;
        } else {
            $category = $this->CR->getBySlug($request->parameters['category']);
            return $this->TR->getBySlug($team, $category)->i->slug;
        }
    }

}
