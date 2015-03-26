<?php

namespace Minicup\Components;

use Minicup\Model\Manager\MigrationsManager;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\EntityNotFoundException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

class MigrateFormComponent extends BaseComponent
{
    /** @var  MigrationsManager */
    private $migrator;

    /** @var  CategoryRepository */
    private $CR;

    /**
     * @param MigrationsManager $migrator
     * @param CategoryRepository $CR
     */
    public function __construct(MigrationsManager $migrator, CategoryRepository $CR)
    {
        $this->migrator = $migrator;
        $this->CR = $CR;
    }

    /**
     * @return Form
     */
    public function createComponentMigrateForm()
    {
        $f = $this->formFactory->create();
        $categories = $this->CR->findAll();
        $select = [];
        foreach ($categories as $category) {
            $select[$category->id] = $category->name;
        }
        $f->addRadioList('category_id', 'Kategorie', $select);
        $f->addCheckbox('confirm', 'Chci přepsat databázi čistými daty z roku 2014!')
            ->setRequired('Jsi si jistý?');
        $f->addCheckbox('truncate', 'Promazat teams & matches');
        $f->addSubmit('migrate', 'Zmigrovat!');
        $f->onSuccess[] = $this->migrateFormSucceed;
        return $f;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws EntityNotFoundException
     */
    public function migrateFormSucceed(Form $form, ArrayHash $values)
    {
        if (!$this->presenter->user->isAllowed('migrations')) {
            $this->presenter->flashMessage('Nemáš práva na migrování databáze!', 'error');
            $this->presenter->redirect('this');
        }
        $category = $this->CR->get($values->category_id);
        if ($values->confirm) {
            $this->migrator->migrateMatches($category, $values->truncate);
        }
        $this->presenter->flashMessage("Kategorie {$category->name} byla úspěšně zmigrována!", 'success');
        $this->presenter->redirect('this');
    }


    public function render()
    {
        $this->template->render();
    }
}

interface IMigrateFormComponentFactory
{
    /** @return MigrateFormComponent */
    public function create();

}