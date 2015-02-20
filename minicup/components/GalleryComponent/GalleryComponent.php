<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Tag;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Nette\Application\AbortException;

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
            $this->template->tags = $this->TR->findMainTags();
        } else {
            $photos = $this->PR->findByTags($this->tags);
            $this->template->tags = $this->tags;
            $this->template->photos = $photos;
        }
        parent::render();
    }

    /**
     * Provide data about tags for select2 by optional term in post parameters
     *
     * @throws AbortException
     */
    public function handleTags()
    {
        $term = isset($this->presenter->request->parameters['term']) ? $this->presenter->request->parameters['term'] : NULL;
        if ($term) {
            $tags = $this->TR->findLikeTerm($term);
        } else {
            $tags = $this->TR->findAll();
        }
        $results = array();
        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $results[] = array('id' => $tag->id, 'text' => $tag->slug);
        }
        $this->presenter->sendJson(array('results' => $results));
    }


}