<?php

namespace components\GalleryComponent;


use Minicup\Components\GalleryComponent;

interface IGalleryComponentFactory
{
    /** @return GalleryComponent */
    public function create();
}