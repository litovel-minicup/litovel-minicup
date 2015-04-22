<?php

namespace Minicup\Components;


use Minicup\Model\Repository\NewsRepository;

class ListOfNewsComponent extends BaseComponent
{
    /** @var NewsRepository */
    private $NR;

    /**
     * @param NewsRepository $NR
     */
    public function __construct(NewsRepository $NR)
    {
        $this->NR = $NR;
    }

    public function render()
    {
        $this->template->news = $this->NR->findLastNews();
        parent::render();
    }
}

interface IListOfNewsComponentFactory
{
    /**
     * @return ListOfNewsComponent
     */
    public function create();

}