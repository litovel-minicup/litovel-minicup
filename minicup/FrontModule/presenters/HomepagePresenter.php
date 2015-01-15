<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Model\Manager\TeamReplicator;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\TeamRepository;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseFrontPresenter
{
    /** @var  TeamReplicator @inject */
    public $replicator;

    /** @var  TeamRepository @inject */
    public $TR;

    public function actionDefault()
    {

    }
}
