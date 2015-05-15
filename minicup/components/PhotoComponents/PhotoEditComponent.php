<?php

namespace Minicup\Components;


use Minicup\AdminModule\Presenters\PhotoPresenter;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Manager\PhotoManager;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Nette\Http\Request;

/**
 * @method onDelete
 * @method onSave
 */
class PhotoEditComponent extends BaseComponent
{
    /** @var callable[] */
    public $onDelete;

    /** @var callable[] */
    public $onSave;

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

    /**
     * @param Photo $photo
     * @param TagRepository $TR
     * @param PhotoRepository $PR
     * @param PhotoManager $PM
     * @param Request $request
     */
    public function __construct(Photo $photo, TagRepository $TR, PhotoRepository $PR, PhotoManager $PM, Request $request)
    {
        parent::__construct();
        $this->TR = $TR;
        $this->PR = $PR;
        $this->PM = $PM;
        $this->photo = $photo;
        $this->request = $request;
    }

    public function render()
    {
        $parent = $this->getParent()->getParent();
        if ($parent instanceof AdminPhotoListComponent) {
            $this->view = "edit";
        } else if ($parent instanceof PhotoUploadComponent) {
            $this->view = "upload";
        } else if ($this->getParent() instanceof PhotoPresenter) {
            $this->view = "edit";
        }
        $this->template->photo = $this->photo;
        parent::render();
    }

    public function handleDelete($lazy = TRUE)
    {
        $this->onDelete($this->photo);
        $this->PM->delete($this->photo, $lazy);
    }

    public function handleSave()
    {
        $this->photo->active = 1;
        $this->PR->persist($this->photo);
        $this->onSave($this->photo);
        $this->redrawControl();
    }

    public function handleToggle()
    {
        $this->photo->active  = $this->photo->active ? 0 : 1;
        $this->PR->persist($this->photo);
        $this->redrawControl();
    }

    public function handleSaveTags()
    {
        $this->photo->removeAllTags();
        if (!$this->request->post['tags']) {
            $this->PR->persist($this->photo);
            return;
        }
        foreach ($this->request->post['tags'] as $id) {
            $this->photo->addToTags((int)$id);
        }
        $this->PR->persist($this->photo);
        $this->redrawControl();
    }
}


interface IPhotoEditComponentFactory
{
    /**
     * @param Photo $photo
     * @return PhotoEditComponent
     */
    public function create(Photo $photo);
}
