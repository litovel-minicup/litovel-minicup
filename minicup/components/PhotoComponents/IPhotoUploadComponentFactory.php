<?php
namespace Minicup\Components;


interface IPhotoUploadComponentFactory
{
    /** @return PhotoUploadComponent */
    public function create();
}