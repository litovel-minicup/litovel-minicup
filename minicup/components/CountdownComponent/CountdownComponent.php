<?php

namespace Minicup\Components;

use Nette\Utils\DateTime;

interface ICountdownComponentFactory
{
    /**
     * @param \DateTime $toDate
     * @return CountdownComponent
     */
    public function create(\DateTime $toDate);

}

class CountdownComponent extends BaseComponent
{
    /**
     * @var \DateTime
     */
    private $toDate;

    /**
     * @param \DateTime $toDate
     */
    public function __construct(\DateTime $toDate)
    {
        parent::__construct();
        $this->toDate = $toDate;
    }

    public function render()
    {

        $this->template->toDate = DateTime::from($this->toDate);
        parent::render();
    }
}

