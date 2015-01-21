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
        $items = [];
        /** @var Category $category */
        foreach ($this->YR->getActualYear()->categories as $category) {
            $items[$category->id] = $category->name;
        }

        $f->addSelect('yearId')->setItems($items);
        $f->addSubmit('submit', 'změnit');
        $f->onSuccess[] = $this->categoryToggleFormSucceeded;
        return $f;
    }

    /***/
    public function categoryToggleFormSucceeded(Form $form, ArrayHash $values)
    {
        dump($values);
    }
}