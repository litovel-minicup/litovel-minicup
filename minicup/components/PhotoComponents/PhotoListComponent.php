<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Photo;

class PhotoListComponent extends BaseComponent
{
    /** @var Photo[] */
    private $photos;

    /**
     * @param Photo[] $photos
     */
    public function __construct(array $photos)
    {
        $this->photos = $photos;
    }

    public function render()
    {
        $this->template->photos = $this->photos;
        parent::render();
    }


}

interface IPhotoListComponentFactory
{
    /**
     * @param Photo[] $photos
     * @return PhotoListComponent
     */
    public function create(array $photos);
}