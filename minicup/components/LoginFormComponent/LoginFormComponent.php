<?php

namespace Minicup\Components;

use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;

interface ILoginFormComponentFactory
{
    /**
     * @param callable $onSuccess
     * @return LoginFormComponent
     */
    public function create(callable $onSuccess);

}

class LoginFormComponent extends BaseComponent
{
    /** @var callable */
    private $onSuccess;

    /**
     * LoginFormComponent constructor.
     * @param callable $onSuccess
     */
    public function __construct(callable $onSuccess)
    {
        $this->onSuccess = $onSuccess;
        parent::__construct();
    }

    const EXPIRATION_SHORT = 60 * 60; // one hour
    const EXPIRATION_LONG = self::EXPIRATION_SHORT * 24 * 7;

    /**
     * @return Form
     */
    public function createComponentLoginForm()
    {
        $form = $this->formFactory->create();
        $form->setMethod(Form::POST);
        $form->addText('username', 'Uživatelské jméno')
            ->setRequired('Prosím, zadejte vaše uživatelské jméno.')
            ->getControlPrototype()->addAttributes(['placeholder' => 'username']);
        $form->addPassword('password', 'Heslo')
            ->setRequired('Prosím vložte vaše heslo.')
            ->getControlPrototype()->addAttributes(['placeholder' => 'password']);
        $form->addCheckbox('remember', 'Zůstat přihlášen');
        $form->addSubmit('submit', 'Přihlásit');

        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            $user = $this->presenter->user;
            try {
                $user->setExpiration($values->remember ? self::EXPIRATION_LONG : self::EXPIRATION_SHORT);
                $user->login($values->username, $values->password);
            } catch (AuthenticationException $e) {
                $form->addError($e->getMessage());
                $this->redrawControl();
                return;
            }
            $this->presenter->flashMessage('Přihlášení proběhlo úspěšně.', 'success');
            $onSuccess = $this->onSuccess;
            $onSuccess($form, $values);
        };
        return $form;
    }
}