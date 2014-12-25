<?php
namespace Minicup\Model\Repository;


class CategoryRepository extends Repository
{
    public function getBySlug($slug)
    {
        $row = $this->connection->select('*')->from($this->getTable())->where('slug = %s', $slug)->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }
} 