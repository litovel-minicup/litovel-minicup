<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IListOfTeamsComponentFactory;
use Minicup\Components\ITeamDetailComponentFactory;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\TeamInfo;
use Nette\Application\UI\Multiplier;

/**
 * Team presenter.
 */
final class TeamPresenter extends BaseFrontPresenter
{

    /** @var IListOfTeamsComponentFactory @inject */
    public $LOTCFactory;

    /** @var ITeamDetailComponentFactory @inject */
    public $TDCFactory;

    public function renderDefault()
    {

    }

    /**
     * @param Category|string $category
     * @param TeamInfo|string $team
     */
    public function renderDetail($category, $team)
    {
        $this->template->team = $team;
    }

    public function createComponentListOfTeamsComponent()
    {
        $CR = $this->CR;
        $me = $this;
        return new Multiplier(function ($categorySlug) use ($CR, $me) {
            $category = $CR->getBySlug($categorySlug);
            return $me->LOTCFactory->create($category);
        });
    }


    public function createComponentTeamDetailComponent()
    {
        $team = $this->getParameter('team');
        /** @var TeamInfo $team */
        return $this->TDCFactory->create($team->team);
    }
}
