<?php
namespace Minicup\Model\Entity;


use LeanMapper\Entity;

/**
 * @property int       $id
 * @property \DateTime $year
 * @property string    $name
 * @property int       $actual
 * @property Day[]     $days m:belongsToMany
 */
class Year extends Entity
{

} 