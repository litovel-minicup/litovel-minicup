<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Match;
use Nette\Http\Url;

interface IMatchDetailComponentFactory
{
    /**
     * @param Match $match
     * @return MatchDetailComponent
     */
    public function create(Match $match);
}

class MatchDetailComponent extends BaseComponent
{
    /** @var Match */
    private $match;
    /** @var Url */
    public $liveServiceUrl;

    public function __construct(Match $match)
    {
        $this->match = $match;
        parent::__construct();
    }

    public function render()
    {
        $this->template->match = $this->match;
        $this->template->liveServiceUrl = $this->liveServiceUrl;
        parent::render();
    }

}