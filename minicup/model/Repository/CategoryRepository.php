<?php
namespace Minicup\Model\Repository;


use Minicup\Model\Entity\Category;

class CategoryRepository extends Repository
{
    /** @var  Category[] categories indexed by slug */
    private $categories;

    private $cache;

    /**
     * @param $slug string
     * @return Category|NULL
     */
    public function getBySlug($slug)
    {
        if (isset($this->categories[$slug])) {
            return $this->categories[$slug];
        }
        $row = $this->connection->select('*')->from($this->getTable())->where('slug = %s', $slug)->fetch();
        if ($row) {
            /** @var Category $category */
            $category = $this->createEntity($row);
            $this->categories[$category->slug] = $category;
            return $category;
        }
        return NULL;
    }
} 