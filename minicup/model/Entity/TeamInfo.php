<?php
namespace Minicup\Model\Entity;


use LeanMapper\Entity;
use LeanMapper\Exception\InvalidStateException;

/**
 * @property    int         $id
 * @property    Category    $category m:hasOne
 * @property-read   Match[] $matches
 * @property    string      $name   czech name of team
 * @property    string      $slug   slug for URL
 * @property    Team        $team m:belongsToOne
 */
class TeamInfo extends Entity
{

    /**
     * @return Match[]
     * @throws InvalidStateException
     */
    public function getMatches()
    {
        $matchTableName = $this->mapper->getTable(Match::class);

        /** @var Match[] $matches */
        $matches = [];
        foreach ($this->row->referencing($matchTableName, 'home_team_info_id') as $match) {
            $matches[$match->id] = $this->entityFactory->createEntity($this->mapper->getEntityClass('match'), $match);
            $matches[$match->id]->makeAlive($this->entityFactory, null, $this->mapper);
        }
        foreach ($this->row->referencing($matchTableName, 'away_team_info_id') as $match) {
            $matches[$match->id] = $this->entityFactory->createEntity($this->mapper->getEntityClass('match'), $match);
            $matches[$match->id]->makeAlive($this->entityFactory, null, $this->mapper);
        }
        /**
         * @param $match1 Match
         * @param $match2 Match
         * @return int
         */
        $cmp = function ($match1, $match2) {
            $match1Start = $match1->matchTerm->start->getTimestamp() + $match1->matchTerm->day->day->getTimestamp();
            $match2Start = $match2->matchTerm->start->getTimestamp() + $match2->matchTerm->day->day->getTimestamp();
            if ($match1Start > $match2Start) {
                return 1;
            } elseif ($match1Start < $match2Start) {
                return -1;
            } else {
                return 0;
            }
        };
        @usort($matches, $cmp);
        return $this->entityFactory->createCollection($matches);
    }
}