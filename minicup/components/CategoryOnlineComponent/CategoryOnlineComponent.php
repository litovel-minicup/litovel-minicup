<?php

namespace Minicup\Components;


use Minicup\FrontModule\Presenters\BaseFrontPresenter;
use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Application\IRouter;
use Nette\Http\IRequest;
use Nette\Http\Request;
use Nette\Http\Session;
use Nette\Http\UrlScript;

interface ICategoryOnlineComponentFactory
{
    /**
     * @param Category $category
     * @return CategoryOnlineComponent
     */
    public function create(Category $category);
}

class CategoryOnlineComponent extends BaseComponent
{

    /** @var Category */
    private $category;

    /**
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
        parent::__construct();
    }

    public function render()
    {
        $this->template->category = $this->category;
        parent::render();
    }

}