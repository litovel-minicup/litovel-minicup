<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Tag;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;

class GalleryComponent extends BaseComponent
{
    /** @var PhotoRepository */
    private $PR;

    /** @var TagRepository */
    private $TR;

    /** @var Tag[] */
    private $tags;

    /**
     * @param Tag[] $tags
     * @param PhotoRepository $PR
     * @param TagRepository $TR
     */
    public function __construct(array $tags, PhotoRepository $PR, TagRepository $TR)
    {
        $this->PR = $PR;
        $this->TR = $TR;
        $this->tags = $tags;
    }

    public function render()
    {
        if (count($this->tags) == 0) {
            $this->view = 'mainTags';
        } else {
            $photos = $this->PR->findByTags($this->tags);
            $this->template->photos = $photos;
        }
        parent::render();
    }


}