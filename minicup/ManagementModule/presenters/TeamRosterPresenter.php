<?php

namespace Minicup\ManagementModule\Presenters;

use Minicup\Components\ITeamRosterManagementComponentFactory;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Repository\TeamInfoRepository;

/**
 * Homepage presenter.
 */
final class TeamRosterPresenter extends BaseManagementPresenter
{

    /** @var TeamInfoRepository @inject */
    public $TIR;

    /** @var ITeamRosterManagementComponentFactory @inject */
    public $TRACF;

    /** @var TeamInfo */
    public $team;

    public function actionDefault($token)
    {
        $this->team = $this->TIR->getByToken($token);
    }

    public function renderDefault()
    {
        $this->template->team = $this->team;
    }

    public function createComponentTeamRosterManagementComponent()
    {
        return $this->TRACF->create($this->team);
    }
}
