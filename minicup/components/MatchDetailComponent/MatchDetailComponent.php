<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Match;
use Minicup\Model\Repository\PhotoRepository;

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
    /** @var callable */
    private $photos;

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
        $this->photos = function () use ($match, $PR) {
            static $photos;
            if ($photos === NULL)
                $photos = $PR->findForMatch($match);
            return $photos;
        };
        parent::__construct();
    }

    public function render()
    {
        $this->template->match = $this->match;
        $this->template->photos = $this->photos;
        parent::render();
    }

    /**
     * @return PhotoListComponent
     */
    public function createComponentPhotoListComponent()
    {
        $photos = $this->photos;
        return $this->PLCF->create($photos());
    }

}