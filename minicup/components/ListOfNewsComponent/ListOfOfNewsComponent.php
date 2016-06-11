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

    /**
     * @param Year           $year
     * @param NewsRepository $NR
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
        $this->template->news = $this->NR->findLastNews($this->year);
        parent::render();
    }
}