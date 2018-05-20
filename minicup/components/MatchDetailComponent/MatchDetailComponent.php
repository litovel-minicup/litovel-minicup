<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Repository\PhotoRepository;
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
    /** @var IPhotoListComponentFactory */
    private $PLCF;
    /** @var Photo[] */
    private $photos;
    /** @var Url */
    public $liveServiceUrl;

    /**
     * MatchDetailComponent constructor.
     * @param Match                      $match
     * @param IPhotoListComponentFactory $PLCF
     * @param PhotoRepository            $PR
     * @throws \Dibi\Exception
     */
    public function __construct(
        Match $match,
        IPhotoListComponentFactory $PLCF,
        PhotoRepository $PR
    )
    {
        $this->match = $match;
        $this->PLCF = $PLCF;
        $this->photos = $PR->findForMatch($match);
        parent::__construct();
    }

    public function render()
    {
        $this->template->match = $this->match;
        $this->template->photos = $this->photos;
        $this->template->liveServiceUrl = $this->liveServiceUrl;
        parent::render();
    }

    /**
     * @return PhotoListComponent
     */
    public function createComponentPhotoListComponent()
    {
        return $this->PLCF->create($this->photos);
    }

}