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

    /** @var ITeamRosterManagementComponentFactory @inject */
    public $TRACF;

    public function renderDefault(TeamInfo $team)
    {
        $this->template->team = $team;
    }

    public function createComponentTeamRosterManagementComponent()
    {
        return $this->TRACF->create($this->team);
    }
}
