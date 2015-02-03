<?php

namespace Minicup\Components;

interface IGalleryComponentFactory
{
    /**
     * @param array $tags
     * @return GalleryComponent
     */
    public function create(array $tags);
}