<?php

namespace Minicup\Model\Entity;

/**
 * @property int           $id
 * @property \DateInterval $start                      datetime of start this MT
 * @property \DateInterval $end                        datetime of end this MT
 * @property string        $location                   location
 * @property Day           $day                        m:hasOne(day_id:day)   in which day is?
 * @property Match[]       $matches                    m:belongsToMany    what matches are played in this term?
 */
class MatchTerm extends BaseEntity
{
    public static $CACHE_TAG = 'matchTerm';
}