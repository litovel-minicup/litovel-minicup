<?php

namespace Minicup\Model\Repository;

use LeanMapper\Connection;
use LeanMapper\IEntityFactory;
use LeanMapper\IMapper;
use Minicup\Model\Entity\Day;
use Minicup\Model\Entity\MatchTerm;

class DayRepository extends BaseRepository
{
    /** @var  YearRepository */
    private $YR;

    /**
     * @param Connection $connection
     * @param IMapper $mapper
     * @param IEntityFactory $entityFactory
     * @param YearRepository $YR
     */
    public function __construct(Connection $connection, IMapper $mapper, IEntityFactory $entityFactory, YearRepository $YR)
    {
        $this->YR = $YR;
        parent::__construct($connection, $mapper, $entityFactory);
    }

    protected function createFluent(/*$filterArg1, $filterArg2, ...*/)
    {
        $year = $this->YR->getSelectedYear();
        return parent::createFluent($year->id);
    }

    /**
     * @param \DibiDateTime $dt
     * @return MatchTerm|null
     */
    public function getByDatetime(\DibiDateTime $dt)
    {
        $row = $this->createFluent()->where('[day] = %s', $dt->format('Y-m-d'))->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }

    /**
     * @return Day[]
     */
    public function findAll()
    {
        return $this->YR->getSelectedYear()->days;
    }


}
