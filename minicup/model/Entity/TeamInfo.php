<?php
namespace Minicup\Model\Entity;


use LeanMapper\Entity;

/**
 * @property int        $id
 * @property Category   $category m:hasOne
 * @property string     $name   czech name of team
 * @property string     $slug   slug for URL
 */
class TeamInfo extends Entity
{

}