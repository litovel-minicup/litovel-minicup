<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\Day;
use Minicup\Model\Entity\MatchTerm;
use Minicup\Model\Entity\Year;

class DayRepository extends BaseRepository
{
    /** @var  Year */
    private $year;

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
        return $this->year->days;
    }


}
