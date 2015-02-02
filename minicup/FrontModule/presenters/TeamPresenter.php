<?php

namespace Minicup\FrontModule\Presenters;

use Minicup\Components\IListOfTeamsComponentFactory;
use Minicup\Components\ITeamDetailComponentFactory;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;
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
    	$this->template->categories = $this->CR->findAll();
    }

    public function renderList(Category $category)
    {
        $this->template->category = $category;
    }

    public function renderDetail(Category $category, Team $team)
    {

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
        return $this->TDCFactory->create($this->getParameter('team'));
    }
}
