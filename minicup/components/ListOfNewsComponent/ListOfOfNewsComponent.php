<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Year;
use Minicup\Model\Repository\NewsRepository;

interface IListOfNewsComponentFactory
{
    /**
     * @return ListOfNewsComponent
     * @param Year $year
     */
    public function create(Year $year);

}

class ListOfNewsComponent extends BaseComponent
{
    /** @var NewsRepository */
    private $NR;

    /** @var Year */
    private $year;

    /**
     * @param Year           $year
     * @param NewsRepository $NR
     */
    public function __construct(Year $year,
                                NewsRepository $NR)
    {
        $this->NR = $NR;
        $this->year = $year;
        parent::__construct();
    }

    public function render()
    {
        $this->template->news = $this->NR->findLastNews($this->year);
        parent::render();
    }
}