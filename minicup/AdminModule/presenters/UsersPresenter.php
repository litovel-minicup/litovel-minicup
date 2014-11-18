<?php

namespace Minicup\AdminModule\Presenters;

use Grido\Components\Filters\Filter;
use Grido\Grid;
use Minicup\Model\Entity;
use Minicup\Model\UserManager;
use Nette;
use Nette\Application\UI\Form;

/**
 * Users grid presenter.
 */
class UsersPresenter extends BaseAdminPresenter
{

    /**
     * @var \DibiConnection
     */
    private $DC;

    /**
     * @var UserManager
     */
    private $UM;

    public function __construct(\DibiConnection $DC, UserManager $UM)
    {
        parent::__construct();
        $this->DC = $DC;
        $this->UM = $UM;
    }

    public function userFormSuccess($form, $values)
    {
        try {
            $this->UM->add(
                $values->username,
                $values->password,
                $values->fullname,
                $values->role);
        } catch (\Exception $ex) {
            $form->addError($ex->getMessage());
            return FALSE;
        }
        $this->flashMessage('Uživatel úspěšně přidán!', 'success');
        $this->redirect('Homepage:default');
    }

    protected function createComponentGrid($name)
    {
        $grid = new Grid($this, $name);
        $fluent = $this->DC->select('*')->from('[user]');
        $grid->model = $fluent;
        $grid->setFilterRenderType(Filter::RENDER_INNER);
        $grid->perPage = 100;
        $grid->addColumnNumber('id', 'id');
        $grid->addColumnText('username', 'Username')
            ->setFilterText()
            ->setSuggestion();
        $grid->addColumnText('role', 'Role')
            ->setSortable()
            ->setFilterText()
            ->setSuggestion();
        return $grid;
    }

    protected function createComponentUserForm()
    {
        $form = new Form();
        $form->addText('username', 'uživatelské jméno')
            ->setRequired();
        $form->addText('fullname', 'celé jméno')
            ->setRequired();
        $form->addPassword('password', 'heslo')
            ->setRequired('Zadejte prosím heslo');
        $form->addPassword('password_check', 'kontrola hesla')
            ->setOmitted(TRUE)
            ->addConditionOn($form['password'], Form::FILLED)
            ->addRule(Form::FILLED, 'Zadejte prosím heslo znovu pro ověření.')
            ->addRule(Form::EQUAL, 'Zřejmě došlo k překlepu, zkuste prosím hesla zadat znovu.', $form['password']);
        $form->addSelect('role', 'role uživatele', Array('admin' => 'administrátor', 'moderator' => 'moderátor'));
        $form->addSubmit('submit', 'vytvořit');
        $form->onSuccess[] = $this->userFormSuccess;
        return $form;
    }

}
