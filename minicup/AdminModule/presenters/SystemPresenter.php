<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Model\Manager\PhotoManager;
use Minicup\Model\Repository\PhotoRepository;
use Nette;

/**
 * Cache presenter.
 */
class SystemPresenter extends BaseAdminPresenter
{
    /** @var PhotoManager @inject */
    public $PM;
    /** @var PhotoRepository @inject */
    public $PR;

    public function actionDefault()
    {
        $this->template->photosToDelete = $this->PR->findUntaggedAndNotActivePhotos($this->YR->getSelectedYear());

    }

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

    public function handleDeleteLatteCaches()
    {
        $latte = $this->context->parameters['tempDir'] . '/cache/latte';
        if (file_exists($latte)) {
            if (static::rmDir($latte)) {
                $this->flashMessage('Latte cache promazány', 'success');
            } else {
                $this->flashMessage('Selhalo promazání latte cache.', 'danger');
            }
        }
    }

    /**
     * @throws \LeanMapper\Exception\InvalidStateException
     */
    public function handleDeleteNotActivePhotos()
    {
        foreach ($this->template->photosToDelete as $p) {
            $this->PM->delete($p, FALSE);
        }
        $this->flashMessage('Fotky smazány', 'success');
    }

    private static function rmDir($dir)
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? static::rmDir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    public function handleDeleteTemp()
    {
        $cache = $this->context->parameters['tempDir'] . '/cache';
        if (static::rmDir($cache)) {
            mkdir($cache);
            $this->flashMessage('Obsah temp smazán!', 'success');
        } else {
            $this->flashMessage('Něco se pokazilo při mazání tempu.', 'danger');
        }
    }
}
