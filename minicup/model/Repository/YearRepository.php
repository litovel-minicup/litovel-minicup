<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\Year;

class YearRepository extends BaseRepository
{
    /**
     * @var Year
     */
    private $selectedYear;

    /**
     * @return Year
     */
    public function getSelectedYear()
    {
        if (!$this->selectedYear) {
            $this->selectedYear = $this->createEntity(
                $this->createFluent()
                    ->where('actual = 1')->fetch());
        }
        return $this->selectedYear;
    }

    /**
     * @param Year $year
     */
    public function setSelectedYear(Year $year)
    {
        $this->selectedYear = $year;
    }

    /**
     * @param string $slug
     * @return Year|NULL
     */
    public function getBySlug($slug)
    {
        return $this->createEntity(
            $this->createFluent()
                ->where('slug = %s', $slug)
                ->fetch());
    }


}
