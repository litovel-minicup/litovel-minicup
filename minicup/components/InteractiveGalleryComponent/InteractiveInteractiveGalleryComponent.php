<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Nette\Application\AbortException;

interface IInteractiveGalleryComponentFactory
{
    /**
     * @param array $tags
     * @return InteractiveGalleryComponent
     */
    public function create(array $tags = NULL);
}

class InteractiveGalleryComponent extends BaseComponent
{
    /** @var PhotoRepository */
    private $PR;

    /** @var TagRepository */
    private $TR;

    /** @var Tag[] */
    private $tags;

    /** @var IPhotoListComponentFactory */
    private $PLCF;

    /**
     * @param Photo[]                    $tags
     * @param IPhotoListComponentFactory $PLCF
     * @param PhotoRepository            $PR
     * @param TagRepository              $TR
     */
    public function __construct(array $tags = NULL,
                                IPhotoListComponentFactory $PLCF,
                                PhotoRepository $PR,
                                TagRepository $TR)
    {
        if (!$tags) {
            $tags = array();
        }
        $this->PR = $PR;
        $this->TR = $TR;
        $this->tags = $tags;
        $this->PLCF = $PLCF;
        parent::__construct();
    }

    public function render()
    {
        $photos = $this->PR->findByTags($this->tags);
        $this->template->selectedTags = array_map(function (Tag $tag) {
            return $tag->id;
        }, $this->tags);
        $this->template->tags = $this->TR->findAll();
        $this->template->photos = $photos;
        parent::render();
    }

    /**
     * Provide data about tags for select2 by optional term in post parameters
     *
     * @throws AbortException
     */
    public function handleTags()
    {
        $params = $this->presenter->request->parameters;
        if (isset($params['term'])) {
            $tags = $this->TR->findLikeTerm($params['term']);
        } else {
            $tags = $this->TR->findAll();
        }
        $results = array();
        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $results[] = array('id' => $tag->id, 'text' => $tag->name ?: $tag->slug);
        }
        $this->redrawControl('photo-list');
        $this->presenter->payload->results = $results;
        $this->presenter->sendPayload();
    }

    public function handleRefresh()
    {
        $ids = $this->presenter->request->parameters['ids'];
        $tags = $ids ? $this->TR->findByIds($ids) : array();
        // $this->presenter->redirect("Gallery:tags", array("tags" => $tags));
        $this->tags = $tags;
        $this->redrawControl('photo-list');
    }

    public function createComponentPhotoListComponent()
    {
        return $this->PLCF->create($this->PR->findByTags($this->tags));
    }
}