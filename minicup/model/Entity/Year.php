<?php

namespace Minicup\Model\Entity;

use LeanMapper\Entity;

/**
 * @property int         $id
 * @property int         $year                       int for year
 * @property string|NULL $name                       optional name
 * @property int         $actual                     flag for actual year
 * @property Day[]       $days m:belongsToMany       game days
 * @property Category[]  $categories m:belongsToMany year categories
 */
class Year extends Entity
{

} 