<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Manager\PhotoManager;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Nette\Application\AbortException;
use Nette\Http\Request;

class PhotoEditComponent extends BaseComponent
{
    /** @var TagRepository */
    private $TR;

    /** @var PhotoRepository */
    private $PR;

    /** @var PhotoManager */
    private $PM;

    /** @var Photo */
    private $photo;

    /** @var Request */
    private $request;

    /** @var callable[] */
    public $onDelete;

    /**
     * @param Photo $photo
     * @param TagRepository $TR
     * @param PhotoRepository $PR
     * @param PhotoManager $PM
     * @param Request $request
     */
    public function __construct(Photo $photo, TagRepository $TR, PhotoRepository $PR, PhotoManager $PM, Request $request)
    {
        $this->TR = $TR;
        $this->PR = $PR;
        $this->PM = $PM;
        $this->photo = $photo;
        $this->request = $request;
    }

    public function render()
    {
        $this->template->photo = $this->photo;
        parent::render();
    }

    /**
     * Provide data about tags for select2 by optional term in post parameters
     *
     * @throws AbortException
     */
    public function handleTags()
    {
        $term = $this->request->getPost('term');
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

    /***/
    public function handleDelete()
    {
        $this->onDelete($this->photo);
        $this->PR->delete($this->photo);
        // TODO use events!
        $this->PM->delete($this->photo);
    }

}