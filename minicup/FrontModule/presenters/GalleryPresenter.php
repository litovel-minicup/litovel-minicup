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
use Minicup\Components\PhotoListComponent;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Repository\TagRepository;

class GalleryPresenter extends BaseFrontPresenter
{
    /** @var IInteractiveGalleryComponentFactory @inject */
    public $GCF;

    /** @var IPhotoListComponentFactory @inject */
    public $PLCF;

    /** @var TagRepository @inject */
    public $TR;

    public function renderDefault()
    {
        $this->template->tags = $this->TR->findMainTags($this->category->year);
    }

    public function renderDetail(Tag $tag)
    {
        $this->template->tag = $tag;
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
        return $this->PLCF->create($this->getParameter('tag')->photos);
    }
}