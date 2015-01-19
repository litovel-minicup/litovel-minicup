<?php

namespace Minicup\Model\Entity;


use LeanMapper\Entity;

/**
 * @property    int         $id
 * @property    string      $pathFull
 * @property    string      $pathThumb
 * @property    Tag[]       $tags m:hasMany
 * @property    \DateTime   $captured
 * @property    \DateTime   $added
 */
class Photo extends Entity
{
    protected function initDefaults()
    {
        $this->added = new \DateTime();
    }

}