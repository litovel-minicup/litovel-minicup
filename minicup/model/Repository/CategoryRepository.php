<?php
namespace Minicup\Model\Repository;


use LeanMapper\Connection;
use LeanMapper\IEntityFactory;
use LeanMapper\IMapper;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Year;
use Nette\InvalidStateException;

class CategoryRepository extends BaseRepository
{
    /** @var YearRepository */
    private $YR;

    /** @var Category */
    private $defaultCategory;

    /**
     * @param Connection     $connection
     * @param IMapper        $mapper
     * @param IEntityFactory $entityFactory
     * @param YearRepository $YR
     */
    public function __construct(Connection $connection, IMapper $mapper, IEntityFactory $entityFactory, YearRepository $YR)
    {
        $this->YR = $YR;
        parent::__construct($connection, $mapper, $entityFactory);
    }

    /**
     * @param           $arg
     * @param Year|NULL $year
     * @return Category|null
     */
    public function getBySlug($arg, Year $year = NULL)
    {
        if ($arg instanceof Category) {
            return $arg;
        }
        if ($year) {
            $row = $this->connection->select('*')->from($this->getTable())->where('[slug] = %s', $arg)->where('[year_id] = %i', $year->id)->fetch();
        } else {
            $row = $this->createFluent()->where('[slug] = %s', $arg)->fetch();
        }
        if ($row) {
            /** @var Category $category */
            $category = $this->createEntity($row);
            return $category;
        }
        return NULL;
    }

    protected function createFluent(/*$filterArg1, $filterArg2, ...*/)
    {
        $year = $this->YR->getSelectedYear();
        return parent::createFluent(array_merge([$year->id], func_get_args()));
    }

    /**
     * @return Category
     */
    public function getDefaultCategory()
    {
        static $defaultCategory;
        if (!$defaultCategory) {
            $row = $this->connection->select('[category.*]')->from('[category]')
                ->leftJoin('year')->on('[year.id] = [category.year_id]')
                ->where('[category.default] = 1')
                ->where('[year.actual] = 1')->fetch();
            if (!$row) {
                throw new InvalidStateException('Default category not found.');
            }
            $defaultCategory = $this->createEntity($row);
        }
        return $defaultCategory;
    }
} 