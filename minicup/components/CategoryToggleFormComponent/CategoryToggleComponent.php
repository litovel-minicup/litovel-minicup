<?php

namespace Minicup\Components;


use Minicup\FrontModule\Presenters\BaseFrontPresenter;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Http\Session;

interface ICategoryToggleFormComponentFactory {
    /** @return CategoryToggleComponent */
    public function create();
}

class CategoryToggleComponent extends BaseComponent {
    /** @var CategoryRepository */
    private $CR;

    /** @var YearRepository */
    private $YR;

    /** @var Session */
    private $session;

    /**
     * @param CategoryRepository $CR
     * @param YearRepository     $YR
     */
    public function __construct(CategoryRepository $CR, YearRepository $YR, Session $session) {
        $this->CR = $CR;
        $this->YR = $YR;
        $this->session = $session->getSection('minicup');
    }

    public function render() {
        $this->template->selectedCategory = $this->presenter->category;
        $this->template->categories = $this->presenter->category->year->categories;
        parent::render();
    }

    public function handleChangeCategory($id) {
        $category = $this->CR->get($id, FALSE);
        /** @var BaseFrontPresenter $presenter */
        $presenter = $this->presenter;
        $presenter->category = $category;
        $this->presenter->redirect('this', array('category' => $category));
    }

}