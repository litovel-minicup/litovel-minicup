<?php

namespace Minicup\Components;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;

class LoginFormComponent extends Control
{

    /** @persistent */
    public $backlink = '';

    public function render()
    {
        $this->template->setFile(__DIR__ . '/LoginFormComponent.latte');
        $this->template->render();
    }

    public function createComponentLoginForm()
    {
        $form = new Form();
        $form->addText('username', 'Uživatelské jméno')
            ->setRequired('Prosím, zadejte vaše uživatelské jméno.');
        $form->addPassword('password', 'Heslo')
            ->setRequired('Prosím vložte vaše heslo.');
        $form->addCheckbox('remember', 'Zůstat přihlášen');
        $form->addSubmit('send', 'Přihlásit');
        if ($this->backlink != '') {
            // TODO: fix request restoring with ajax
            // $form->getElementPrototype()->class[] = 'ajax';
        }
        $form->onSuccess[] = $this->loginFormValidated;
        return $form;
    }

    /**
     * @param Form      $form
     * @param ArrayHash $values
     */
    public function loginFormValidated($form, $values)
    {
        $user = $this->presenter->user;
        try {
            $user->login($values->username, $values->password);
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
            $this->redrawControl();
            return;
        }
        if ($values->remember || in_array('admin', $user->roles)) {
            $user->setExpiration('14 days', FALSE);
        } else {
            $user->setExpiration('20 minutes', TRUE);
        }
        $this->presenter->flashMessage('Přihlášení proběhlo úspěšně.', 'success');
        $this->presenter->redirect(':Admin:Homepage:');
    }


    public function handleLogOut()
    {
        $this->getPresenter(TRUE)->getUser()->logout();
        $this->presenter->flashMessage('Byl jste odhlášen.');
        $this->presenter->redirect(':Front:Homepage:');
    }

}
