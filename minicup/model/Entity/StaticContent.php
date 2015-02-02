<?php

namespace Minicup\Model\Entity;

use LeanMapper\Entity;

/**
 * @property int            $id
 * @property string         $content
 * @property \DibiDateTime  $updated
 * @property User           $author m:hasOne
 */
class StaticContent extends Entity
{

}
