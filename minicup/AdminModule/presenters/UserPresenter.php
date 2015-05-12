<?php

namespace Minicup\AdminModule\Presenters;

use Grido\Components\Filters\Filter;
use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Model\Entity;
use Minicup\Model\Manager\UserManager;
use Nette\Application\UI\Form;
use Nette\InvalidArgumentException;
use Nette\Utils\ArrayHash;

/**
 * Users administration presenter.
 */
class UserPresenter extends BaseAdminPresenter
{

    /** @var Connection @inject */
    public $DC;

    /** @var UserManager @inject */
    public $UM;

    /**
     * @param Form      $form
     * @param ArrayHash $values
     */
    public function userFormSuccess(Form $form, ArrayHash $values)
    {
        try {
            $this->UM->add(
                $values->username,
                $values->password,
                $values->fullname,
                $values->role);
        } catch (InvalidArgumentException $ex) {
            $form->addError($ex->getMessage());
            return;
        }
        $this->flashMessage('Uživatel úspěšně přidán!', 'success');
        $this->redirect('Homepage:default');
    }

    /**
     * @param $name
     * @return Grid
     */
    protected function createComponentGrid($name)
    {
        $grid = new Grid($this, $name);
        $g->setFilterRenderType(Filter::RENDER_INNER);
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

    /**
     * @return Form
     */
    protected function createComponentUserForm()
    {
        $f = $this->formFactory->create();
        $f->addText('username', 'uživatelské jméno')
            ->setRequired();
        $f->addText('fullname', 'celé jméno')
            ->setRequired();
        $f->addPassword('password', 'heslo')
            ->setRequired('Zadejte prosím heslo');
        $f->addPassword('password_check', 'kontrola hesla')
            ->setOmitted(TRUE)
            ->addConditionOn($f['password'], Form::FILLED)
            ->addRule(Form::FILLED, 'Zadejte prosím heslo znovu pro ověření.')
            ->addRule(Form::EQUAL, 'Zřejmě došlo k překlepu, zkuste prosím hesla zadat znovu.', $f['password']);
        $f->addSelect('role', 'role uživatele', Array('admin' => 'administrátor', 'moderator' => 'moderátor'));
        $f->addSubmit('submit', 'vytvořit');
        $f->onSuccess[] = $this->userFormSuccess;
        return $f;
    }
}
