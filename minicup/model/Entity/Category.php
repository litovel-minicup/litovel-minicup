<?php

namespace Minicup\Model\Entity;

use LeanMapper\Entity;

/**
 * @property int $id
 * @property string $name czech name of category
 * @property string $slug slug for URL
 * @property Team[] $teams m:belongsToMany
 */
class Category extends Entity
{

}
