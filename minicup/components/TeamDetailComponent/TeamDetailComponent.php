<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Player;
use Minicup\Model\Entity\Team;
use Minicup\Model\Manager\MatchManager;
use Minicup\Model\Manager\TagManager;
use Minicup\Model\Repository\BaseRepository;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\PlayerRepository;
use Minicup\Model\Repository\TeamRepository;

interface ITeamDetailComponentFactory
{
    /**
     * @param     $team Team
     * @return TeamDetailComponent
     */
    public function create(Team $team);
}

class TeamDetailComponent extends BaseComponent
{
    /** @var Team */
    public $team;

    /** @var TeamRepository */
    private $TR;

    /** @var PhotoRepository */
    private $PR;

    /** @var TagManager */
    private $TM;

    /** @var MatchManager */
    private $MM;

    /** @var IListOfMatchesComponentFactory */
    private $LOMCF;

    /** @var IStaticContentComponentFactory */
    private $SCCF;

    /** @var IPhotoListComponentFactory */
    private $PhLCF;

    /** @var ITeamHistoryComponentFactory */
    private $THCF;

    /** @var IPlayerListComponentFactory */
    private $PlLCF;

    /** @var Player[] */
    private $players;

    /**
     * @param Team                           $team
     * @param TeamRepository                 $TR
     * @param PlayerRepository               $PR
     * @param PhotoRepository                $photoRepository
     * @param TagManager                     $TM
     * @param Team                           $team
     * @param TeamRepository                 $TR
     * @param PlayerRepository               $PR
     * @param TagManager                     $TM
     * @param MatchManager                   $MM
     * @param IListOfMatchesComponentFactory $LOMCF
     * @param IStaticContentComponentFactory $SCCF
     * @param IPhotoListComponentFactory     $PhLCF
     * @param ITeamHistoryComponentFactory   $THCF
     * @param IPlayerListComponentFactory    $PlLCF
     * @throws \Dibi\Exception
     */
    public function __construct(
        Team $team,
        TeamRepository $TR,
        PlayerRepository $PR,
        PhotoRepository $photoRepository,
        TagManager $TM,
        IListOfMatchesComponentFactory $LOMCF,
        IStaticContentComponentFactory $SCCF,
        IPhotoListComponentFactory $PhLCF,
        ITeamHistoryComponentFactory $THCF,
        IPlayerListComponentFactory $PlLCF,
        MatchManager $MM
    )
    {
        parent::__construct();
        $this->team = $team;
        $this->TR = $TR;
        $this->LOMCF = $LOMCF;
        $this->SCCF = $SCCF;
        $this->PhLCF = $PhLCF;
        $this->TM = $TM;
        $this->MM = $MM;
        $this->THCF = $THCF;
        $this->PlLCF = $PlLCF;
        $this->PR = $photoRepository;
        $this->PlLCF = $PlLCF;
        $this->players = $PR->findByTeamWithConfirmedGoals($team->i);
    }

    public function render()
    {
        $this->team->i->tag = $this->TM->getTag($this->team);
        $this->template->team = $this->team;
        $this->template->players = $this->players;
        $this->template->hasStarted = $this->MM->isStarted($this->team->category);
        parent::render();
    }

    /**
     * @return ListOfMatchesComponent
     */
    public function createComponentListOfMatchesComponent()
    {
        return $this->LOMCF->create($this->team);
    }

    /**
     * @return StaticContentComponent
     */
    public function createComponentStaticContentComponent()
    {
        return $this->SCCF->create($this->team, $this->team->category->year);
    }

    /**
     * @return PhotoListComponent
     * @throws \LeanMapper\Exception\InvalidArgumentException
     */
    public function createComponentPhotoListComponent()
    {
        $tag = $this->TM->getTag($this->team);
        return $this->PhLCF->create($tag ? $this->PR->findByTag($tag, BaseRepository::ORDER_ASC) : []);
    }

    /**
     * @return TeamHistoryComponent
     */
    public function createComponentTeamHistoryComponent()
    {
        return $this->THCF->create($this->team);
    }

    /**
     * @return PlayerListComponent
     */
    public function createComponentPlayerListComponent()
    {
        return $this->PlLCF->create($this->players);
    }

}