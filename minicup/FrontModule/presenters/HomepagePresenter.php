<?php

namespace Minicup\FrontModule\Presenters;
use Minicup\Components\IPhotoUploadComponent;
use Minicup\Model\Repository\TagRepository;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseFrontPresenter
{
    /** @var  IPhotoUploadComponent @inject */
    public $PUC;

    /** @var  TagRepository @inject */
    public $TR;

    public function createComponentPhotoUploadComponent()
    {
        return $this->PUC->create();
    }
}
