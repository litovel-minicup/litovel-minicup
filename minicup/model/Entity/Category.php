<?php

namespace Minicup\Model\Entity;

use LeanMapper\Entity;

/**
 * @property int        $id
 * @property string     $name                                       czech name of category
 * @property string     $slug                                       slug for URL
 * @property Team[]     $teams m:belongsToMany                      actually teams in this category
 * @property Match[]    $matches m:belongsToMany                    matches in this category
 * @property Team[]     $allTeams m:belongsToMany(::category_id)    all historical teams in category
 * @property Year       $year m:hasOne                              year for this category
 * @property int        $default                                    flag if it's default category
 */
class Category extends Entity
{

}
