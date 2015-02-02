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
     * @param Tag[]|string[] $tags
     * @param PhotoRepository $PR
     * @param TagRepository $TR
     */
    public function __construct(array $tags, PhotoRepository $PR, TagRepository $TR)
    {
        $this->PR = $PR;
        $this->TR = $TR;
        if ($tags) {
            if (!$tags[0] instanceof Tag) {
                $tags = $this->TR->findBySlugs($tags);
            }
        }
        $this->tags = $tags;
    }


}