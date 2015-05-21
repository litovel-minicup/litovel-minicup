<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Model\Manager\PhotoManager;
use Nette;

/**
 * Cache presenter.
 */
class SystemPresenter extends BaseAdminPresenter
{
    /** @var PhotoManager @inject */
    public $PM;

    public function handleDeleteAllEntityCaches()
    {
        $this->CM->cleanAllEntityCaches();
        $this->flashMessage('Cache všech entity aplikace byly úspěšně promazány.', 'success');
    }

    public function handleDeleteAllCachedPhoto()
    {
        $failed = $this->PM->cleanCachedPhotos();
        if (!$failed) {
            $this->flashMessage('Všechny fotky v cache byly promazány!', 'success');
        } else {
            $this->flashMessage('Promazání fotek ' . join(', ', $failed) . 'selhalo, zbytek v pořádku.', 'warning');
        }
    }

}
