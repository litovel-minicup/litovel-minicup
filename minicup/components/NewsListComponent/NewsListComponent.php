<?php

namespace Minicup\Components;


use Minicup\Model\Repository\NewsRepository;

class NewsListComponent extends BaseComponent
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

interface INewsListComponentFactory
{
    /**
     * @return NewsListComponent
     */
    public function create();

}