<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Model\Manager\MigrationsManager;
use Minicup\Model\Repository\CategoryRepository;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseFrontPresenter
{
    /** @var  MigrationsManager @inject */
    public $migrator;

    /** @var  CategoryRepository @inject */
    public $CR;

    public function actionMigrate()
    {
        $this->migrator->migrate($this->CR->getBySlug('starsi'));
    }
}
