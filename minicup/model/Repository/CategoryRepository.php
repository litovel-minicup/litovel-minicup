<?php
namespace Minicup\Model\Repository;


use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Year;
use Nette\InvalidStateException;

class CategoryRepository extends BaseRepository
{
    /** @var  Year */
    private $year;

    /** @var  Category[] categories indexed by slug */
    private $categories;

    /**
     * @param Year $year
     */
    public function injectYear(Year $year)
    {
        $this->year = $year;
    }

    protected function createFluent(/*$filterArg1, $filterArg2, ...*/)
    {
        return parent::createFluent($this->year->id);
    }


    /**
     * @param $slug string
     * @return Category|NULL
     */
    public function getBySlug($slug)
    {
        if (isset($this->categories[$slug])) {
            return $this->categories[$slug];
        }
        $row = $this->createFluent()->where('[slug] = %s', $slug)->fetch();
        if ($row) {
            /** @var Category $category */
            $category = $this->createEntity($row);
            $this->categories[$category->slug] = $category;
            return $category;
        }
        return NULL;
    }

    /**
     * @return Category|NULL
     */
    public function getDefaultCategory()
    {
        $row = $this->createFluent()->where('[default] = 1')->fetch();
        if ($row) {
            return $this->createEntity($row);
        }
        throw new InvalidStateException('Default category not found.');
    }
} 