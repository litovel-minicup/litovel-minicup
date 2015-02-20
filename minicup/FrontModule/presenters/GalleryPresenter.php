<?php
/**
 * Created by PhpStorm.
 * User: thejoeejoee
 * Date: 20.2.15
 * Time: 21:13
 */

namespace Minicup\FrontModule\Presenters;


use Minicup\Components\GalleryComponent;
use Minicup\Components\IGalleryComponentFactory;
use Minicup\Model\Repository\TagRepository;

class GalleryPresenter extends BaseFrontPresenter
{
    /** @var IGalleryComponentFactory @inject */
    public $GCF;

    /** @var TagRepository @inject */
    public $TR;
    
    /**
     * @return GalleryComponent
     */
    protected function createComponentGalleryComponent()
    {
    	return $this->GCF->create($this->TR->findBySlugs(array('vyhlaseni')));
    }

}