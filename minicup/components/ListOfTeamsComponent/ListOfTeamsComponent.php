<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Minicup\Model\Repository\TeamRepository;

class ListOfTeamsComponent extends BaseComponent
{
    /** @var  TeamRepository */
    private $TR;

    /** @var  TeamInfoRepository */
    private $TIR;

    /** @var  CategoryRepository */
    private $CR;

    /** @var  Category */
    private $category;

    public function __construct(Category $category, TeamRepository $TR, TeamInfoRepository $TIR, CategoryRepository $CR)
    {
        parent::__construct();
        $this->category = $category;
        $this->TR = $TR;
        $this->TIR = $TIR;
        $this->CR = $CR;
    }

    public function render()
    {
        $template = $this->template;
        $template->category = $this->category;
        $template->teams = $this->category->teams;
        $template->render();
    }

}

interface IListOfTeamsComponentFactory
{
    /**
     * @param $category
     * @return ListOfTeamsComponent
     */
    public function create(Category $category);

}