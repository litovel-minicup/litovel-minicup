<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Year;
use Minicup\Model\Repository\NewsRepository;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;

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

    /** @var Cache */
    private $cache;

    /** @var int */
    private $actual = NewsRepository::DEFAULT_LIMIT;

    /** @var int */
    private $step = NewsRepository::DEFAULT_LIMIT;

    /**
     * @param Year           $year
     * @param NewsRepository $NR
     * @param IStorage       $storage
     */
    public function __construct(Year $year,
                                NewsRepository $NR,
                                IStorage $storage)
    {
        $this->NR = $NR;
        $this->year = $year;
        $this->cache = new Cache($storage);
        parent::__construct();
    }

    public function render()
    {
        $this->template->news = $this->NR->findLastNews($this->year, $this->actual);
        $this->template->step = $this->step;
        $this->template->actual = $this->actual;
        $this->template->max = $this->NR->getNewsCountInYear($this->year);

        parent::render();
    }

    public function handleShow($count)
    {
        $this->actual = $count;
        $this->redrawControl('news');
    }
}