<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Application\UI\Form;
use Nette\Http\Session;
use Nette\Utils\ArrayHash;

class CategoryToggleFormComponent extends BaseComponent
{
    /** @var  CategoryRepository */
    private $CR;

    /** @var  YearRepository */
    private $YR;

    /** @var  Session */
    private $session;

    /**
     * @param CategoryRepository $CR
     * @param YearRepository $YR
     */
    public function __construct(CategoryRepository $CR, YearRepository $YR, Session $session)
    {
        $this->CR = $CR;
        $this->YR = $YR;
        $this->session = $session->getSection('minicup');
    }

    /** @return Form */
    protected function createComponentCategoryToggleForm()
    {
        $f = $this->FF->create();
        $f->setMethod(Form::GET);
        $select = $f->addSelect('categoryId');
        $items = [];
        $default = 0;
        /** @var Category $category */
        foreach ($this->YR->getActualYear()->categories as $category) {
            $items[$category->id] = $category->name;
            if ($category->slug === $this->session['category']) {
                $default = $category->id;
            }
        }
        $select->setItems($items);
        if ($default) {
            $select->setValue($default);
        }

        $f->addSubmit('submit', 'změnit');
        $f->onSuccess[] = $this->categoryToggleFormSucceeded;
        return $f;
    }

    /***/
    public function categoryToggleFormSucceeded(Form $form, ArrayHash $values)
    {
        /** @var Category $category */
        $category = $this->CR->get($values->categoryId);
        $this->session['category'] = $category->slug;
        $this->presenter->flashMessage('Preferovaná kategorie úspěšně změněna!','success');
        $this->presenter->redirect('this', ['category' => $category]);
    }
}