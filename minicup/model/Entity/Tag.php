<?php

namespace Minicup\Model\Entity;

use LeanMapper\Entity;

/**
 * @property    int     $id
 * @property    Photo[] $photos m:hasMany(:photo_tag)
 * @property    string  $name
 * @property    string  $slug
 * @property    int     $isGallery = 0
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