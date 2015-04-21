<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\MatchTerm;

class MatchTermRepository extends BaseRepository
{
    /**
     * @param \DateTime $dt
     * @return MatchTerm|null
     */
    public function getByStart(\DateTime $dt)
    {
        $date = clone $dt;
        $time = clone $dt;
        $date->setTime(0, 0, 0);
        $time->setDate(0, 0, 0);
        $row = $this->createFluent()
            ->where('[match_term.start] = %s', $time->format('H:i:s'), ' AND [day.day] = %s', $date->format('Y-m-d'))
            ->leftJoin('day')
            ->on('[day.id] = [match_term.day_id]')->fetch();
        return $row ? $this->createEntity($row) : NULL;
    }

    /**
     * @param \DateTime $dt
     * @return MatchTerm|NULL
     */
    public function getInTime(\DateTime $dt)
    {
        $date = clone $dt;
        $time = clone $dt;
        $date->setTime(0, 0, 0);
        $time->setDate(0, 0, 0);
        $row = $this->createFluent()
            ->where('%s BETWEEN [match_term.start] AND [match_term.end]', $time->format('H:i:s'))
            ->where('[day.day] = %s', $date->format('Y-m-d'))
            ->leftJoin('day')
            ->on('[day.id] = [match_term.day_id]')->fetch();

        return $row ? $this->createEntity($row) : NULL;
    }
}
