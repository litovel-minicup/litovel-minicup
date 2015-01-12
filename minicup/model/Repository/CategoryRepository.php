<?php
namespace Minicup\Model\Repository;


use Minicup\Model\Entity\Category;

class CategoryRepository extends Repository
{
    /**
     * @param $slug string
     * @return Category|NULL
     */
    public function getBySlug($slug)
    {
        $row = $this->connection->select('*')->from($this->getTable())->where('slug = %s', $slug)->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }
} 