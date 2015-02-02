<?php

namespace Minicup\Model\Entity;


use LeanMapper\Entity;

/**
 * @property int         $id
 * @property string      $pathFull                      path for full image
 * @property string      $pathThumb                     path for thumbnail of image
 * @property Tag[]       $tags m:hasMany                tags for photo
 * @property \DateTime   $captured                      when is photo captured?
 * @property User        $author m:hasOne(author_id)    from who is this photo?
 */
class Photo extends Entity
{
    protected function initDefaults()
    {
        $this->added = new \DateTime();
    }

}