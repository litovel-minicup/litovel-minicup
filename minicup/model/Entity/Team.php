<?php

namespace Minicup\Model\Entity;

use LeanMapper\Entity;

/**
 * @property int           $id
 * @property string        $name czech name of team
 * @property string        $slug slug for URL
 * @property-read int      $order order of team in table
 * @property-read Category $category m:hasOne category where is team in
 * @property-read Match[]       $matches m:belongsToMany(home_team_id) m:filter(matches)
 */
class Team extends Entity
{


}
