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
use Minicup\Model\Repository\TagRepository;

class GalleryPresenter extends BaseFrontPresenter
{
    /** @var IInteractiveGalleryComponentFactory @inject */
    public $GCF;

    /** @var TagRepository @inject */
    public $TR;
    
    /**
     * @return InteractiveGalleryComponent
     */
    protected function createComponentInteractiveGalleryComponent()
    {
    	return $this->GCF->create($this->getParameter('tags'));
    }

    public function renderTags(array $tags)
    {

    }
}