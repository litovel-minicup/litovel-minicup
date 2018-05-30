<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Application\IRouter;
use Nette\Http\IRequest;
use Nette\Http\Session;

interface IYearToggleComponentFactory
{
    /**
     * @param Category $category
     * @return YearToggleComponent
     */
    public function create(Category $category);
}

class YearToggleComponent extends BaseComponent
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

    /**@var Category */
    private $category;

    /**
     * @param Category           $category
     * @param CategoryRepository $CR
     * @param YearRepository     $YR
     * @param IRouter            $router
     * @param IRequest           $request
     * @param Session            $session
     */
    public function __construct(Category $category,
                                CategoryRepository $CR,
                                YearRepository $YR,
                                IRouter $router,
                                IRequest $request,
                                Session $session)
    {
        $this->CR = $CR;
        $this->YR = $YR;
        $this->router = $router;
        $this->request = $request;
        $this->category = $category;
        $this->session = $session->getSection('minicup');
        parent::__construct();
    }

    public function render()
    {
        $this->template->years = $this->YR->findArchiveYears();
        $this->template->actualYear = $this->YR->getActualYear();
        $this->template->category = $this->category;
        parent::render();
    }

    /**
     * @param $year
     * @throws \Nette\Application\AbortException
     */
    public function handleChangeYear($year)
    {
        $actualCategorySlug = $this->category->slug;
        $year = $this->YR->get($year, FALSE);
        $category = $this->CR->getBySlug($actualCategorySlug, $year);
        $this->session->offsetSet('category', $category->id);

        $this->presenter->redirect(':Front:Homepage:default', ['category' => $category]);
    }

    /**
     * @return string
     * @throws \Nette\Application\UI\InvalidLinkException
     */
    public function getActualYearLink()
    {
        return $this->link('changeYear!', ['year' => $this->YR->getActualYear()->id]);
    }

}