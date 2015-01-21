<?php

namespace Minicup\Model\Entity;

use LeanMapper\Entity;

/**
 * @property int    $id
 * @property string $name czech name of category
 * @property string $slug slug for URL
 * @property Team[] $teams m:belongsToMany
 * @property Match[] $matches m:belongsToMany
 * @property-read Team[] $allTeams m:belongsToMany(::category_id)
 * @property Year $year m:hasOne
 */
class Category extends Entity
{

}
