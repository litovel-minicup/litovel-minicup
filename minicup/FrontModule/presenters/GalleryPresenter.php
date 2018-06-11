<?php
/**
 * Created by PhpStorm.
 * User: thejoeejoee
 * Date: 20.2.15
 * Time: 21:13
 */

namespace Minicup\FrontModule\Presenters;


use Minicup\Components\IInteractiveGalleryComponentFactory;
use Minicup\Components\InteractiveGalleryComponent;
use Minicup\Components\IPhotoListComponentFactory;
use Minicup\Components\IPhotoPresentationComponentFactory;
use Minicup\Components\PhotoListComponent;
use Minicup\Components\PhotoPresentationComponent;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Repository\BaseRepository;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;

class GalleryPresenter extends BaseFrontPresenter
{
    /** @var IInteractiveGalleryComponentFactory @inject */
    public $GCF;

    /** @var IPhotoListComponentFactory @inject */
    public $PLCF;

    /** @var TagRepository @inject */
    public $TR;

    /** @var IPhotoPresentationComponentFactory @inject */
    public $PPF;

    /** @var PhotoRepository @inject */
    public $PR;

    public function renderDefault()
    {
        $this->template->tags = $this->TR->findMainTags($this->category->year);
        $this->template->photosCount = $this->PR->countYearPhotos($this->category->year);
    }

    public function renderDetail(Tag $tag)
    {
        $this->template->tag = $tag;
    }

    /**
     * @return PhotoPresentationComponent
     */
    protected function createComponentPhotoPresentationComponent()
    {
        return $this->PPF->create(
            $this->category->year
        );
    }


    /**
     * @return InteractiveGalleryComponent
     */
    protected function createComponentInteractiveGalleryComponent()
    {
        return $this->GCF->create($this->getParameter('tags'));
    }

    /**
     * @return PhotoListComponent
     */
    protected function createComponentPhotoListComponent()
    {
        /** @var Tag $tag */
        $tag = $this->getParameter('tag');
        return $this->PLCF->create($this->PR->findByTag($tag));
    }
}