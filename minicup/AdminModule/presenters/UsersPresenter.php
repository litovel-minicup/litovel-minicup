<?php

namespace Minicup\AdminModule\Presenters;

use Nette,
    Minicup\Model\Entity;

/**
 * Users grid presenter.
 */
class UsersPresenter extends BasePresenter {

    /**
     * @var \DibiConnection
     */
    private $DC;

    public function __construct(\DibiConnection $DC) {
        parent::__construct();
        $this->DC = $DC;
    }

    protected function createComponentGrid($name) {
        $grid = new \Grido\Grid($this, $name);
        $fluent = $this->DC->select('*')->from('[user]');
        $grid->model = $fluent;
        $grid->addColumnText('username', 'Username')
                ->setFilterText()
                ->setSuggestion();
        $grid->addColumnText('role', 'Role')
                ->setSortable()
                ->setFilterText()
                ->setSuggestion();
        $grid->addFilterSelect('gender', 'Gender', array(
            '' => '',
            'female' => 'female',
            'male' => 'male'
        ));
        $operations = array('print' => 'Print', 'delete' => 'Delete');
        $grid->setExport();
        return $grid;
    }

}
