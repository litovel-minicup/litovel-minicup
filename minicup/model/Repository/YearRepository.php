<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\Year;
use Nette\InvalidStateException;

class YearRepository extends BaseRepository {
    /**
     * @var Year
     */
    private $selectedYear;

    /**
     * @return Year
     * @throws InvalidStateException
     */
    public function getSelectedYear() {
        if (!$this->selectedYear) {
            $row = $this->createFluent()->where('actual = 1')->fetch();
            if (!$row) {
                throw new InvalidStateException('Table year has not actual year.');
            }
            $this->selectedYear = $this->createEntity($row);
        }
        return $this->selectedYear;
    }

    /**
     * @param Year $year
     * @return Year
     */
    public function setSelectedYear(Year $year = NULL) {
        return $this->selectedYear = $year;
    }

    public function getActualYear() {
        $row = $this->createFluent()->where('actual = 1')->fetch();
        if (!$row) {
            throw new InvalidStateException('Table year has not actual year.');
        }
        return $this->createEntity($row);
    }

    /**
     * @param string $slug
     * @return Year|NULL
     */
    public function getBySlug($slug) {
        $row = $this->createFluent()
            ->where('slug = %s', $slug)
            ->fetch();
        if ($row) {
            return $this->createEntity($row);
        }
        return NULL;
    }
}
