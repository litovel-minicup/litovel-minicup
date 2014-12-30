<?php
namespace Minicup\Model\Entity;


use LeanMapper\Entity;

/**
 * @property int         $id
 * @property \DateTime   $day
 * @property Year        $year m:hasOne(year_id:year)
 * @property MatchTerm[] $matchTerms m:belongsToMany
 */
class Day extends Entity
{


}