<?php

namespace Minicup\Model\Entity;


/**
 * @property int       $id
 * @property string    $filename          filename
 * @property Tag[]     $tags m:hasMany    tags for photo
 * @property \DateTime $added             added datetime
 * @property \DateTime $taken             phoho taken datetime
 * @property int       $active
 *
 * @method removeAllTags()
 * @method addToTags(Tag $tag)
 */
class Photo extends BaseEntity
{
    public static $CACHE_TAG = 'photo';

    protected function initDefaults()
    {
        $this->added = new \DateTime();
    }
}