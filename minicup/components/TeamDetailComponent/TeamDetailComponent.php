<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Team;
use Minicup\Model\Manager\TagManager;
use Minicup\Model\Repository\TeamRepository;

class TeamDetailComponent extends BaseComponent
{
    /** @var Team */
    public $team;

    /** @var TeamRepository */
    private $TR;

    /** @var TagManager */
    private $TM;

    /** @var IListOfMatchesComponentFactory */
    private $LOMCF;

    /** @var IStaticContentComponentFactory */
    private $SCCF;

    /** @var IPhotoListComponentFactory */
    private $PLCF;

    /** @var ITeamHistoryComponent */
    private $THCF;

    /**
     * @param Team $team
     * @param TeamRepository $TR
     * @param TagManager $TM
     * @param IListOfMatchesComponentFactory $LOMCF
     * @param IStaticContentComponentFactory $SCCF
     * @param IPhotoListComponentFactory $PLCF
     * @param ITeamHistoryComponent $THCF
     */
    public function __construct(Team $team,
                                TeamRepository $TR,
                                TagManager $TM,
                                IListOfMatchesComponentFactory $LOMCF,
                                IStaticContentComponentFactory $SCCF,
                                IPhotoListComponentFactory $PLCF,
                                ITeamHistoryComponent $THCF)
    {
        parent::__construct();
        $this->team = $team;
        $this->TR = $TR;
        $this->LOMCF = $LOMCF;
        $this->SCCF = $SCCF;
        $this->PLCF = $PLCF;
        $this->TM = $TM;
        $this->THCF = $THCF;
    }

    public function render()
    {
        $this->template->team = $this->team;
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
        return $this->SCCF->create($this->team);
    }

    /**
     * @return PhotoListComponent
     */
    public function createComponentPhotoListComponent()
    {
        $tag = $this->TM->getTag($this->team);
        return $this->PLCF->create($tag->photos);
    }

    /**
     * @return TeamHistoryComponent
     */
    public function createComponentTeamHistoryComponent()
    {
        return $this->THCF->create($this->team);
    }
}

interface ITeamDetailComponentFactory
{
    /**
     * @param $team Team
     * @return TeamDetailComponent
     */
    public function create(Team $team);
}