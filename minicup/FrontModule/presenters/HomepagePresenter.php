<?php

namespace Minicup\FrontModule\Presenters;

use Nette,
    Minicup\Model\Entity;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {
    /**
     *
     * @var \Minicup\Components\ICategoryTableComponentFactory
     */
    private $CTCFactory;

    public function __construct(\Minicup\Components\ICategoryTableComponentFactory $CTCFactory) {
        parent::__construct();
        $this->CTCFactory = $CTCFactory;
    }
    
    public function renderDefault() {
    }

    public function createComponentCategoryTableComponent() {
        return $this->CTCFactory->create();
    }
}
