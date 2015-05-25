<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Photo;

class PhotoListComponent extends BaseComponent
{
    /** @var Photo[] */
    private $photos;

    /** @var int */
    private $actual;

    /** @var int */
    private $step;

    /**
     * @param array $photos
     * @param int $initial
     * @param int $step
     */
    public function __construct(array $photos, $initial = 12, $step = 18)
    {
        $this->photos = $photos;
        $this->actual = $initial;
        $this->step = $step;
    }

    public function render()
    {
        $this->template->step = $this->step;
        $this->template->actual = $this->actual;
        $this->template->photos = $this->actual ? array_slice($this->photos, 0, $this->actual) : $this->photos;
        $this->template->max = count($this->photos);
        $this->template->allPhotos = $this->photos;
        parent::render();
    }

    public function handleShow($count)
    {
        $this->actual = $count;
        $this->redrawControl('photos');
    }

    public function handleAll()
    {
        $data = array();
        foreach ($this->photos as $photo) {
            $data[] = array(
                "href" => $this->presenter->link(':Media:medium', $photo->filename),
                "title" => NULL
            );
        }
        $this->presenter->sendJson($data);
    }


}

interface IPhotoListComponentFactory
{
    /**
     * @param Photo[] $photos
     * @param int $initial
     * @param int $step
     * @return PhotoListComponent
     */
    public function create(array $photos, $initial = 12, $step = 18);
}