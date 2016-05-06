<?php

namespace Minicup\Components;


use Minicup\AdminModule\Presenters\PhotoPresenter;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Manager\CacheManager;
use Minicup\Model\Manager\PhotoManager;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Nette\Http\Request;

interface IPhotoEditComponentFactory
{
    /**
     * @param Photo $photo
     * @return PhotoEditComponent
     */
    public function create(Photo $photo);
}

/**
 * @method onDelete(Photo $photo)
 * @method onSave(Photo $photo)
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

    /** @var CacheManager */
    private $CM;

    /** @var Photo */
    private $photo;

    /** @var Request */
    private $request;

    /**
     * @param Photo           $photo
     * @param TagRepository   $TR
     * @param PhotoRepository $PR
     * @param PhotoManager    $PM
     * @param Request         $request
     * @param CacheManager    $CM
     */
    public function __construct(Photo $photo,
                                TagRepository $TR,
                                PhotoRepository $PR,
                                PhotoManager $PM,
                                Request $request,
                                CacheManager $CM)
    {
        parent::__construct();
        $this->TR = $TR;
        $this->PR = $PR;
        $this->PM = $PM;
        $this->photo = $photo;
        $this->request = $request;
        $this->CM = $CM;
    }

    public function render()
    {
        $parent = $this->getParent()->getParent();
        if ($parent instanceof AdminPhotoListComponent) {
            $this->view = 'edit';
        } else if ($parent instanceof PhotoUploadComponent) {
            $this->view = 'upload';
        } else if ($this->getParent() instanceof PhotoPresenter) {
            $this->view = 'edit';
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
        $this->photo->active = $this->photo->active ? 0 : 1;
        $this->PR->persist($this->photo);
        $this->redrawControl();
    }

    public function handleSaveTags()
    {
        if (!$this->request->post['tags']) {
            $this->PR->persist($this->photo);
            return;
        }
        $this->photo->removeAllTags();
        foreach ($this->request->post['tags'] as $id) {
            /** @var Tag $tag */
            $tag = $this->TR->get($id);
            if ($tag->teamInfo) {
                $this->CM->cleanByEntity($tag->teamInfo->team);
            }
            $this->photo->addToTags($tag);
        }
        $this->PR->persist($this->photo);
        $this->redrawControl();
    }
}
