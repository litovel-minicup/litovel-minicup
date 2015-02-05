<?php

namespace Minicup\Model\Entity;


use LeanMapper\Entity;

/**
 * @property int         $id
 * @property string      $filename          filename
 * @property Tag[]       $tags m:hasMany    tags for photo
 * @property \DateTime   $added             added datetime
 */
class Photo extends Entity
{
    protected function initDefaults()
    {
        $this->added = new \DateTime();
    }
}