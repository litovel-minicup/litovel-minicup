<?php

namespace Minicup\AdminModule\Presenters;
use Minicup\Components\IMigrateFormComponentFactory;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseAdminPresenter {
    /** @var IMigrateFormComponentFactory @inject */
    public $migrateFormFactory;

    public function createComponentMigrateForm() {
        return $this->migrateFormFactory->create();
    }
}
