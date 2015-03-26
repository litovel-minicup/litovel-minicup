<?php

namespace Minicup\Components;

use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;

class LoginFormComponent extends BaseComponent
{
    /**
     * @return Form
     */
    public function createComponentLoginForm()
    {
        $form = $this->formFactory->create();
        $form->addText('username', 'Uživatelské jméno')
            ->setRequired('Prosím, zadejte vaše uživatelské jméno.');
        $form->addPassword('password', 'Heslo')
            ->setRequired('Prosím vložte vaše heslo.');
        $form->addCheckbox('remember', 'Zůstat přihlášen');
        $form->addSubmit('submit', 'Přihlásit');
        $form->onSuccess[] = $this->loginFormValidated;
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function loginFormValidated(Form $form, ArrayHash $values)
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
}
