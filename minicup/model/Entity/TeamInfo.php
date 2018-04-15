<?php

namespace Minicup\Model\Entity;


use Dibi\DateTime;
use LeanMapper\Exception\InvalidStateException;

/**
 * @property        int                $id
 * @property        Category           $category m:hasOne      category
 * @property-read   Match[]            $matches                matches for this team
 * @property        Player[]           $players m:belongsToMany
 * @property        string             $name                   czech name of team
 * @property        string             $slug                   slug for URL
 * @property        Team|NULL          $team m:belongsToOne    actually connected team
 * @property        StaticContent|NULL $staticContent m:hasOne
 * @property        Tag|NULL           $tag m:hasOne
 * @property        string|NULL        $dressColor             Color of team dress
 * @property        string|NULL        $dressColorSecondary    Secondary color of team dress
 * @property        string|NULL        $trainerName            Name of team trainer
 * @property        string|NULL        $description            Description edited by team
 * @property        string             $password               Password for access to administration
 * @property        DateTime           $updated                Last time update
 * @property        string|NULL        $authToken              Auth token for REST API
 */
class TeamInfo extends BaseEntity
{
    public static $CACHE_TAG = 'teamInfo';

    /**
     * @return Match[]
     * @throws InvalidStateException
     * @throws \LeanMapper\Exception\InvalidArgumentException
     */
    public function getMatches()
    {
        $matchTableName = $this->mapper->getTable('match');

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
         * compare matches by start datetime
         * @param $match1 Match
         * @param $match2 Match
         * @return int
         */
        $cmp = function ($match1, $match2) {
            $match1Start = $match1->matchTerm->start->setDate(0, 0, 0)->getTimestamp() + $match1->matchTerm->day->day->getTimestamp();
            $match2Start = $match2->matchTerm->start->setDate(0, 0, 0)->getTimestamp() + $match2->matchTerm->day->day->getTimestamp();
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