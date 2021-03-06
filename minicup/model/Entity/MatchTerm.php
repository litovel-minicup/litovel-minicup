<?php

namespace Minicup\Model\Entity;


use DateTime;

/**
 * @property int      $id
 * @property DateTime $start                      datetime of start this MT
 * @property DateTime $end                        datetime of end this MT
 * @property string   $location                   location
 * @property Day      $day                        m:hasOne(day_id:day)   in which day is?
 * @property Match[]  $matches                    m:belongsToMany    what matches are played in this term?
 */
class MatchTerm extends BaseEntity
{
    public static $CACHE_TAG = 'matchTerm';
}