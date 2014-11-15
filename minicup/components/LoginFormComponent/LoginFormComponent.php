<?php

namespace Minicup\Components;

use \Nette\Application\UI\Control,
    \Nette\Application\UI\Form;

class LoginFormComponent extends Control {

    /** @persistent */
    public $backlink = '';

    public function render() {
        $this->template->setFile(__DIR__ . '/LoginFormComponent.latte');
        $this->template->render();
    }

    public function createComponentLoginForm() {
        $form = new Form();
        $form->addText('username', 'Uživatelské jméno')
                ->setRequired('Prosím, zadejte vaše uživatelské jméno.');
        $form->addPassword('password', 'Heslo')
                ->setRequired('Prosím vložte vaše heslo.');
        $form->addCheckbox('remember', 'Zůstat přihlášen');
        $form->addSubmit('send', 'Přihlásit');
        $form->getElementPrototype()->class = 'ajax';
        $form->onSuccess[] = $this->loginFormValidated;
        return $form;
    }

    /**
     * @param Form $form
     * @param \Nette\Utils\ArrayHash $values
     */
    public function loginFormValidated($form, $values) {
        $user = $this->getPresenter(TRUE)->getUser();
        try {
            $user->login($values->username, $values->password);
        } catch (Nette\Security\AuthenticationException $e) {
            $this->flashMessage($e->getMessage(), 'error');
            $this->redrawControl();
            return;
        }
        if ($values->remember || in_array('admin', $user->roles)) {
            $user->setExpiration('14 days', FALSE);
        } else {
            $user->setExpiration('20 minutes', TRUE);
        }
        $this->presenter->redirect(':Admin:Homepage:');
    }
    
    public function handleLogOut() {
        $this->getPresenter(TRUE)->getUser()->logout();
        $this->presenter->flashMessage('Byl jste odhlášen.');
        $this->presenter->redirect(':Front:Homepage:');
    }

}
