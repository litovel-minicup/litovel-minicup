<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\Year;

class YearRepository extends Repository
{
    /**
     * @var Year
     */
    private $actualYear;

    /**
     * @return Year
     */
    public function getActualYear()
    {
        if (!$this->actualYear) {
            $this->actualYear = $this->createEntity(
                $this->connection
                    ->select('*')
                    ->from($this->getTable())
                    ->where('actual = 1')->fetch());
        }
        return $this->actualYear;
    }
}
