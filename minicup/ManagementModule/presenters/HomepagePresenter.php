<?php

namespace Minicup\ManagementModule\Presenters;

use Minicup\Model\Entity\TeamInfo;
use Nette\Application\UI\Form;
use Nette\ArrayHash;
use Nette\Forms\Controls\TextInput;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseManagementPresenter
{

    protected function isLoggedToManageTeam()
    {
        if ($this->action == 'default') {
            return true;
        }
        return parent::isLoggedToManageTeam();
    }

    public function actionDefault(TeamInfo $team)
    {
        if (parent::isLoggedToManageTeam()) {
            $this->redirect('TeamRoster:', [$this->team]);
        }
    }

    public function renderDefault()
    {
        $this->template->isLogged = false;
    }

    public function createComponentLoginFormComponent()
    {
        $f = $this->formFactory->create();

        $f->addPassword('pin');
        $f->addSubmit('login');
        $f->addProtection();

        $f->onSuccess[] = function (Form $form, ArrayHash $values) {
            if ($values['pin'] == $this->team->password) {
                $this->getHttpResponse()->setCookie(self::MANAGEMENT_COOKIE, $this->team->id, 0);
                $this->flashMessage('Přihlášení proběhlo úspěšně!', 'success');
                $this->redirect('TeamRoster:', [$this->team]);
            } else {
                /** @var TextInput $pin */
                $this->flashMessage('Nesprávný PIN.', 'error');
            }
        };

        return $f;
    }
}
