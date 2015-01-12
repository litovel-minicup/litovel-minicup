<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IListOfTeamsComponentFactory;
use Minicup\Components\ITeamDetailComponentFactory;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;

/**
 * Team presenter.
 */
final class TeamPresenter extends BaseFrontPresenter
{

    /** @var IListOfTeamsComponentFactory @inject */
    public $LOTCFactory;

    /** @var ITeamDetailComponentFactory @inject */
    public $TDCFactory;


    public function renderDefault(Category $category = NULL)
    {
        if ($category) {
            $this->template->category = $category;
        } else {
            $this->template->categories = $this->CR->findAll();
        }
    }

    public function renderDetail(Category $category, Team $team)
    {

    }

    public function createComponentListOfTeamsComponent()
    {
        return $this->LOTCFactory->create();
    }


    public function createComponentTeamDetailComponent()
    {
        return $this->TDCFactory->create($this->getParameter('team'));
    }
}
