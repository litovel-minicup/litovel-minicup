<?php

namespace Minicup\Model\Entity;

use LeanMapper\Entity;

/**
 * @property    int         $id
 * @property    Photo[]     $photos m:hasMany(:photo_tag) m:filter(activePhotos)    photos for this tag
 * @property    string      $name                                                   name for tag
 * @property    string      $slug                                                   unique slug
 * @property    int         $isGallery = 0                                          flag for gallery
 * @property    Photo|NULL  $main m:hasOne(main_photo_id)                           main photo for tag
 */
class Tag extends Entity
{
    /**
     * @return bool
     */
    public function getIsGallery()
    {
        return (bool) $this->row->is_gallery;
    }

    /**
     * @param $isGallery
     */
    public function setIsGallery($isGallery)
    {
        $this->row->is_gallery = (int) $isGallery;
    }
}