<?php

namespace Minicup\AdminModule\Presenters;


use Minicup\Components\IMatchFormComponentFactory;
use Minicup\Model\Entity\Category;

class MatchPresenter extends BaseAdminPresenter
{
    /** @var IMatchFormComponentFactory @inject */
    public $MFCF;


    public function renderConfirm(Category $category)
    {
        $this->template->category = $category;
    }

    /***/
    public function createComponentMatchFormComponent()
    {
        return $this->MFCF->create($this->params['category'], 5);
    }
}