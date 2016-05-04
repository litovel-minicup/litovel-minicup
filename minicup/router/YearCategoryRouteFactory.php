<?php

namespace Minicup\Router;


use Dibi\DriverException;
use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Application\Routers\Route;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\InvalidArgumentException;
use Nette\InvalidStateException;
use Nette\Object;
use Nette\Utils\Strings;

class YearCategoryRouteFactory extends Object
{

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
                                Session $session)
    {
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
    public function __invoke($mask, array $metadata = array(), $flags = 0, $required = FALSE)
    {
        return $this->route($mask, $metadata, $flags, $required);
    }

    /**
     * @param string    $mask
     * @param array     $metadata
     * @param int       $flags
     * @param bool|TRUE $required
     * @return Route
     */
    public function route($mask, array $metadata = array(), $flags = 0, $required = FALSE)
    {
        $metadata[static::DEFAULT_KEY] = $this->getMetadata($required);
        $mask .= ($required ? static::DEFAULT_REQUIRED_PATTERN : static::DEFAULT_OPTIONAL_PATTERN);

        return new Route($mask, $metadata, $flags);
    }

    /**
     * @param bool $requiredCategory
     * @return array
     */
    public function getMetadata($requiredCategory)
    {
        $CR = $this->categoryRepository;
        $YR = $this->yearRepository;
        $metadata = array(
            Route::FILTER_IN => function ($slug) use ($CR, $YR) {
                // TEMPORARILY SOLUTION
                // TODO: remove after search engines reindex old project
                if ($category = $CR->getBySlug($slug, $YR->getBySlug('2014'))) {
                    return $category;
                }
                if (!$m = Strings::matchAll($slug, '#([0-9]{4})-([\w]*)#')) {
                    return NULL;
                }
                list(, $yearSlug, $categorySlug) = $m[0];
                $year = $YR->getBySlug($yearSlug);
                $category = $CR->getBySlug($categorySlug, $year);

                return ($year && $category) ? $category : NULL;
            },
            Route::FILTER_OUT => function ($category) use ($CR, $YR) {
                if (!$category instanceof Category) {
                    $category = $CR->getBySlug($category, $YR->getBySlug('2014'));
                }
                $slug = "{$category->year->year}-{$category->slug}";
                if (!Strings::match($slug, '#([0-9]{4})-([\w]*)#')) {
                    throw new InvalidStateException;
                }
                return $slug;
            }
        );
        try {
            if (!$requiredCategory) {
                static $category;
                if (!$category) {
                    $category = $this->categoryRepository->get($this->session->offsetGet('category'), FALSE);
                }
                $metadata[Route::VALUE] = $category ?: $this->categoryRepository->getDefaultCategory();
            }

        } catch (DriverException $e) {

        }
        return $metadata;
    }
}