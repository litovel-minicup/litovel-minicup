<?php
namespace Minicup\Model\Repository;


class CategoryRepository extends Repository
{
    public function getBySlug($slug)
    {
        return $this->createEntity($this->connection->select('*')->from($this->getTable())->where('slug = %s', $slug)->fetch());
    }
} 