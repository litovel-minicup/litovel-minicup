<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Model\Entity\Team;
use Minicup\Model\Manager\MigrationsManager;
use Minicup\Model\Manager\TeamReplicator;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\TeamRepository;
use Nette\Bridges\Framework\TracyBridge;
use Tracy\Debugger;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseFrontPresenter
{
    /** @var  TeamReplicator @inject */
    public $replicator;

    /** @var  CategoryRepository @inject */
    public $CR;

    /** @var  TeamRepository @inject */
    public $TR;

    public function actionDefault()
    {

    }
}
