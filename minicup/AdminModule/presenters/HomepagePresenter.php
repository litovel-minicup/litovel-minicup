<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Components\IPhotoUploadComponentFactory;
use Minicup\Components\PhotoUploadComponent;
use Minicup\Model\Manager\MigrationsManager;
use Minicup\Model\Manager\ReorderManager;
use Minicup\Model\Repository\TagRepository;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseAdminPresenter
{
    /** @var ReorderManager @inject */
    public $reorder;

    /** @var IPhotoUploadComponentFactory @inject */
    public $PUC;

    /** @var TagRepository @inject */
    public $TR;

    /**
     * @return PhotoUploadComponent
     */
    public function createComponentPhotoUploadComponent()
    {
        return $this->PUC->create();
    }

    /** @var MigrationsManager @inject */
    public $MM;

    public function actionDefault()
    {
        $this->MM->migrateMatches($this->CR->getBySlug('mladsi'), TRUE, FALSE);
    }


}
