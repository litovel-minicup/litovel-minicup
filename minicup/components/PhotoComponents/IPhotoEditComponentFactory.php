<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Photo;

interface IPhotoEditComponentFactory
{
    /**
     * @param Photo $photo
     * @return PhotoEditComponent
     */
    public function create(Photo $photo);
}