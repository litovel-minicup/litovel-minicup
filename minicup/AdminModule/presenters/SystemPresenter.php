<?php

namespace Minicup\AdminModule\Presenters;

use Minicup\Model\Manager\CacheManager;
use Nette;

/**
 * Cache presenter.
 */
class SystemPresenter extends BaseAdminPresenter
{
    /** @var  CacheManager @inject */
    public $CM;

    public function handleDeleteAllCache()
    {
        $this->CM->cleanAllEntityCaches();
    }

}
