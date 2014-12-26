<?php

namespace Minicup\Model\Entity;

use LeanMapper\Entity;

/**
 * @property int           $id
 * @property string        $name czech name of team
 * @property string        $slug slug for URL
 * @property-read int      $order order of team in table
 * @property-read Category $category m:hasOne category where is team in
 * @property-read Match[]       $matches m:useMethods
 */
class Team extends Entity
{

    public function getMatches()
    {
        // TODO: solve getMatches through both columns (match.home_team_id, match.away_team_id)
        return [];
    }

}
