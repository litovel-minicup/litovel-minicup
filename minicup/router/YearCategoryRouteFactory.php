<?php

namespace Minicup\Router;


use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Application\Routers\Route;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Object;
use Nette\Utils\Strings;

class YearCategoryRouteFactory extends Object {

    const DEFAULT_REQUIRED_PATTERN = '<category>';
    const DEFAULT_OPTIONAL_PATTERN = '[!<category>]';

    const DEFAULT_KEY = 'category';

    /**
     * @var YearRepository
     */
    private $yearRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /** @var SessionSection */
    private $session;

    /**
     * @param CategoryRepository $categoryRepository
     * @param YearRepository     $yearRepository
     * @param Session            $session
     */
    public function __construct(CategoryRepository $categoryRepository,
                                YearRepository $yearRepository,
                                Session $session) {
        $this->categoryRepository = $categoryRepository;
        $this->yearRepository = $yearRepository;
        $this->session = $session->getSection('minicup');
    }

    /**
     * @param           $mask
     * @param array     $metadata
     * @param int       $flags
     * @param bool|TRUE $required
     * @return Route
     */
    public function __invoke($mask, $metadata = array(), $flags = 0, $required = FALSE) {
        return $this->route($mask, $metadata, $flags, $required);
    }

    /**
     * @param string    $mask
     * @param array     $metadata
     * @param int       $flags
     * @param bool|TRUE $required
     * @return Route
     */
    public function route($mask, $metadata = array(), $flags = 0, $required = FALSE) {
        $metadata[static::DEFAULT_KEY] = $this->getMetadata($required);
        $mask = $mask . ($required ? static::DEFAULT_REQUIRED_PATTERN : static::DEFAULT_OPTIONAL_PATTERN);

        return new Route($mask, $metadata, $flags);
    }

    /**
     * @param bool $requiredCategory
     * @return array
     */
    public function getMetadata($requiredCategory) {
        $CR = $this->categoryRepository;
        $metadata = array(
            Route::FILTER_IN => function ($slug) {
                if (!$m = Strings::matchAll($slug, '#([0-9]{4})-([\w]*)#')) {
                    return NULL;
                }
                list(, $yearSlug, $categorySlug) = $m[0];
                $year = $this->yearRepository->getBySlug($yearSlug);
                $category = $this->categoryRepository->getBySlug($categorySlug, $year);

                return $year && $category ? $category : NULL;
            },
            Route::FILTER_OUT => function ($category) use ($CR) {
                if (!$category instanceof Category) {
                    $category = $CR->getBySlug($category);
                }
                return "{$category->year->year}-{$category->slug}";
            }
        );
        if (!$requiredCategory) {
            $category = $this->categoryRepository->get($this->session->offsetGet('category'), FALSE);
            $metadata[Route::VALUE] = $category ? $category : $this->categoryRepository->getDefaultCategory();
        }
        return $metadata;
    }
}