<?php

namespace Minicup\Presenters;

use Nette,
    App\Model;

/**
 * Sign in/out presenters.
 */
class SignPresenter extends Nette\Application\UI\Presenter {

    /** @persistent */
    public $backlink = '';

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm() {
        $form = new Nette\Application\UI\Form;
        $form->addText('username', 'Uživatelské jméno')
                ->setRequired('Prosím, zadejte vaše uživatelské jméno.');
        $form->addPassword('password', 'Heslo')
                ->setRequired('Prosím vložte vaše heslo.');
        $form->addCheckbox('remember', 'Zůstat přihlášen');
        $form->addSubmit('send', 'Přihlásit');

        $form->onSuccess[] = $this->signInFormSucceeded;
        return $form;
    }

    public function signInFormSucceeded($form, $values) {
        if ($values->remember) {
            $this->getUser()->setExpiration('14 days', FALSE);
        } else {
            $this->getUser()->setExpiration('20 minutes', TRUE);
        }
        try {
            $this->getUser()->login($values->username, $values->password);
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
        $this->restoreRequest($this->backlink);
        $this->redirect('Admin:Homepage:');
    }

    public function actionOut() {
        $this->getUser()->logout();
        $this->flashMessage('Byl jste odhlášen');
        $this->redirect('in');
    }

}
