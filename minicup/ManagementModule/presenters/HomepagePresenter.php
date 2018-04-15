<?php

namespace Minicup\ManagementModule\Presenters;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseManagementPresenter
{

    public function renderDefault()
    {

    }

    public function createComponentLoginFormComponent()
    {
        $f = $this->formFactory->create();


        $f->addPassword('pin');
        $f->addSubmit('login');

        return $f;
    }
}
