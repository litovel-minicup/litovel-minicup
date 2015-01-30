<?php

namespace Minicup\Components;


use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Http\Session;

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

    public function render()
    {
        $this->template->selectedCategory = $this->CR->getBySlug($this->session['category']);
        $this->template->categories = $this->YR->getSelectedYear()->categories;
        parent::render();
    }

    public function handleChangeCategory($slug)
    {
        $category = $this->CR->getBySlug($slug);
        $this->session['category'] = $category->slug;
        $this->presenter->flashMessage('Preferovaná kategorie úspěšně změněna!', 'success');
        $this->presenter->redirect('this', array('category' => $category));
    }

}