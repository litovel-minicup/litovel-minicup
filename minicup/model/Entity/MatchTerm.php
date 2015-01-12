<?php

namespace Minicup\Model\Entity;

use LeanMapper\Entity;

/**
 * @property int        $id
 * @property \DateTime  $start
 * @property \DateTime  $end
 * @property string     $location
 * @property Day        $day m:hasOne(day_id:day)
 * @property Match[]    $matches m:belongsToMany
 *
 */
class MatchTerm extends Entity
{

}