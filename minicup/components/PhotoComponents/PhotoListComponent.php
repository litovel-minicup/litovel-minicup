<?php

namespace Minicup\Components;


use Latte\Runtime\Filters;
use Minicup\Model\Entity\Photo;

interface IPhotoListComponentFactory
{
    /**
     * @param Photo[] $photos
     * @param int     $initial
     * @param int     $step
     * @return PhotoListComponent
     */
    public function create(array $photos, $initial = 12, $step = 18);
}

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
     * @param int   $initial
     * @param int   $step
     */
    public function __construct(array $photos,
                                $initial = 12,
                                $step = 18)
    {
        $this->photos = $photos;
        $this->actual = $initial;
        $this->step = $step;
        parent::__construct();
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
        $data = [];
        $count = count($this->photos);
        foreach ($this->photos as $i => $photo) {
            $i++;
            $data[] = [
                'href' => $this->presenter->link(':Media:medium', $photo->filename),
                'title' => "Fotka {$i}. z {$count}" . ($photo->author ? " - {$photo->author}" : '') . ' - ' . Filters::date($photo->taken, 'G:i j. n. Y')
            ];
        }
        $this->presenter->sendJson($data);
    }


}