<?php

namespace Minicup\Components;


use Minicup\FrontModule\Presenters\BaseFrontPresenter;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Application\IRouter;
use Nette\Http\IRequest;
use Nette\Http\Request;
use Nette\Http\Session;
use Nette\Http\UrlScript;

interface ICategoryToggleComponentFactory
{
    /** @return CategoryToggleComponent */
    public function create();
}

class CategoryToggleComponent extends BaseComponent
{
    /** @var CategoryRepository */
    private $CR;

    /** @var YearRepository */
    private $YR;

    /** @var Session */
    private $session;

    /** @var IRouter */
    private $router;

    /** @var IRequest */
    private $request;

    /**
     * @param CategoryRepository $CR
     * @param YearRepository     $YR
     * @param Session            $session
     * @param IRouter            $router
     * @param IRequest           $request
     */
    public function __construct(CategoryRepository $CR,
                                YearRepository $YR,
                                Session $session,
                                IRouter $router,
                                IRequest $request)
    {
        $this->CR = $CR;
        $this->YR = $YR;
        $this->router = $router;
        $this->session = $session->getSection('minicup');
        $this->request = $request;
        parent::__construct();
    }

    public function render()
    {
        $this->template->selectedCategory = $this->presenter->category;
        $this->template->categories = $this->presenter->category->year->categories;
        parent::render();
    }

    public function handleChangeCategory($id)
    {
        $category = $this->CR->get($id, FALSE);
        $this->session->offsetSet('category', $category->id);
        /** @var BaseFrontPresenter $presenter */
        $presenter = $this->presenter;
        $presenter->category = $category;

		$this->presenter->redirect(':Front:Homepage:default', ['category' => $category]);
    }

}