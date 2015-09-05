<?php

namespace Minicup\Router;


use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Application\Routers\Route;
use Nette\Object;
use Nette\Utils\Strings;
use Tracy\Debugger;

class YearCategoryRouteFactory extends Object {

    const DEFAULT_PATTERN = '[!<category>/]';

    const DEFAULT_KEY = 'category';

    protected $metadata = array();

    /**
     * @var YearRepository
     */
    private $yearRepository;


    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param CategoryRepository $categoryRepository
     * @param YearRepository     $yearRepository
     */
    public function __construct(CategoryRepository $categoryRepository,
                                YearRepository $yearRepository) {
        $this->categoryRepository = $categoryRepository;
        $this->yearRepository = $yearRepository;
    }

    /**
     * @param string $mask
     * @param array  $metadata
     * @param int    $flags
     * @return Route
     */
    public function __invoke($mask, $metadata = array(), $flags = 0) {
        return $this->route($mask, $metadata, $flags);
    }

    /**
     * @param       $mask
     * @param array $metadata
     * @param int   $flags
     * @return Route
     */
    public function route($mask, $metadata = array(), $flags = 0) {
        $metadata[static::DEFAULT_KEY] = $this->getMetadata();
        $mask = static::DEFAULT_PATTERN . $mask;

        return new Route($mask, $metadata, $flags);
    }

    /**
     * @return array
     */
    public function getMetadata() {
        if (!$this->metadata) {
            $this->metadata = array(
                Route::FILTER_IN => function ($slug) {
                    if (!$m = Strings::matchAll($slug, '#([0-9]{4})-([\w]*)#')) {
                        return NULL;
                    }
                    list(, $yearSlug, $categorySlug) = $m[0];
                    $year = $this->yearRepository->getBySlug($yearSlug);
                    $category = $this->categoryRepository->getBySlug($categorySlug, $year);

                    return $year && $category ? $category : NULL;
                },
                Route::FILTER_OUT => function (Category $category) {
                    return "{$category->year->year}-{$category->slug}";
                },
                Route::VALUE => $this->categoryRepository->getDefaultCategory()
            );
        }
        return $this->metadata;
    }


}