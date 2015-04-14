<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Category;
use Minicup\Model\Manager\MigrationsManager;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\EntityNotFoundException;
use Minicup\Model\Repository\YearRepository;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

class MigrateFormComponent extends BaseComponent
{
    /** @var  MigrationsManager */
    private $migrator;

    /** @var  CategoryRepository */
    private $CR;

    /** @var YearRepository */
    private $YR;

    /**
     * @param MigrationsManager $migrator
     * @param CategoryRepository $CR
     */
    public function __construct(MigrationsManager $migrator, CategoryRepository $CR, YearRepository $YR)
    {
        $this->migrator = $migrator;
        $this->CR = $CR;
        $this->YR = $YR;
    }

    /**
     * @return Form
     */
    public function createComponentMigrateForm()
    {
        $f = $this->formFactory->create();
        $years = $this->YR->findAll();
        $select = array();
        foreach ($years as $year) {
            foreach ($year->categories as $category) {
                $select[$category->id] = $year->year . ' - ' . $category->name;
            }
        }
        $f->addRadioList('category_id', 'Kategorie', $select);
        $f->addCheckbox('confirm', 'Chci přepsat databázi čistými daty z roku 2014!')
            ->setRequired('Jsi si jistý?');
        $f->addCheckbox('truncate', 'Promazat teams & matches');
        $f->addCheckbox('with_score', 'Vložit skore a vygenerovat historii');
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
        if (!$this->presenter->user->isAllowed('migration')) {
            $this->presenter->flashMessage('Nemáš práva na migrování databáze!', 'error');
            $this->presenter->redirect('this');
        }
        /** @var Category $category */
        $category = $this->CR->get($values->category_id);
        if ($values->confirm) {
            $this->migrator->migrateMatches($category, $values->truncate, $values->with_score);
        }
        $this->presenter->flashMessage("Kategorie {$category->name} byla úspěšně zmigrována!", 'success');
        $this->presenter->redirect('this');
    }
}

interface IMigrateFormComponentFactory
{
    /** @return MigrateFormComponent */
    public function create();

}