<?php

namespace Minicup\AdminModule\Presenters;


use Grido\Components\Columns\Column;
use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Components\IMatchFormComponentFactory;
use Minicup\Components\ITeamRosterManagementComponentFactory;
use Minicup\Components\MatchFormComponent;
use Minicup\Components\TeamRosterManagementComponent;
use Minicup\Misc\GridHelpers;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Player;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Repository\PlayerRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

class TeamPresenter extends BaseAdminPresenter
{


    /**
     * Mapp db name => Real name
     */
    const TEAM_INFO_GRID_LABELS = [
        'id' => '#',
        'slug' => 'Detail na webu',
        'name' => 'Název',
        'slug' => 'Slug',
        'trainer_name' => 'Trenér',
        'dress_color' => 'Barva dresu',
        'dress_color_secondary' => 'Sekundární barva'
    ];

    /** @var IMatchFormComponentFactory @inject */
    public $MFCF;

    /** @var Connection @inject */
    public $connection;

    /** @var TeamInfoRepository @inject */
    public $TIR;

    /** @var PlayerRepository @inject */
    public $PR;

    /** @var ITeamRosterManagementComponentFactory @inject */
    public $TRACF;

    /**
     * @param Category $category
     */
    public function renderList(Category $category)
    {
        $this->template->category = $category;
    }


    public function renderRoster($team)
    {
        $team = $this->TIR->get($team);
        $this->template->team = $team;
    }

    /**
     * @return MatchFormComponent
     */
    public function createComponentMatchFormComponent()
    {
        return $this->MFCF->create($this->params['category'], 5);
    }

    /**
     * @param string $name
     * @return Grid
     */
    public function createComponentMatchesGridComponent($name)
    {
        $connection = $this->connection;
        $TIR = $this->TIR;
        $CR = $this->CR;

        $that = $this;
        $g = new Grid();

        $f = $connection->select('[ti].*')
            ->from('[team_info]')->as('ti')
            ->where('ti.[category_id] = ', $this->getParameter('category')->id);

        $g->setModel($f);
        $g->addColumnNumber('id', $this::TEAM_INFO_GRID_LABELS['id']);

        // links
        $g->addActionHref('slug', 'Detail na webu')->setCustomHref(function ($row) use ($CR, $that) {
            $category = $CR->get($row->category_id, FALSE);
            return $that->link(':Front:Team:detail', ['team' => $row->slug, 'category' => $category]);
        });

        $g->addActionHref('id', 'Editace soupisky')->setCustomHref(function ($row) use ($TIR, $that) {
            return $that->link('Team:roster', ['team' => $row->id]);
        });

        // Name && slug
        $this->addTeamInfoEditableText($g, 'name');
        $this->addTeamInfoEditableText($g, 'slug');

        // Treiner name
        $this->addTeamInfoEditableText($g, 'trainer_name');


        // Dress color
        $this->addTeamInfoEditableText($g, 'dress_color');
        $this->addTeamInfoEditableText($g, 'dress_color_secondary');

        return $g;
    }

    public function createComponentRosterGridComponent($name)
    {
        $connection = $this->connection;
        $TIR = $this->TIR;
        $CR = $this->CR;

        $that = $this;
        $g = new Grid();

        $f = $connection->select('[p].*')
            ->from('[player]')->as('p')
            ->where('p.[team_info_id] = ', $this->getParameter('team'));

        $g->setModel($f);
        $g->defaultSort = ['number' => 'ASC', 'secondary_number' => 'ASC'];

        $this->addPlayerEditableText($g, 'number', 'Číslo');
        $this->addPlayerEditableText($g, 'name', 'Jméno');
        $this->addPlayerEditableText($g, 'surname', 'Příjmení');

        // Treiner name
        $this->addPlayerEditableText($g, 'secondary_number', 'Alt. číslo');

        $g->addActionEvent('delete', 'Smazat', function ($id) use ($that) {
            $that->PR->delete($id);
        })->setConfirm('Really?');


        return $g;
    }

    public function createComponentPlayerFormComponent()
    {
        $f = $this->formFactory->create();

        $f->addText('number', 'Číslo')
            ->setRequired(true)
            ->addRule(Form::INTEGER)
            ->addRule(Form::RANGE, NULL, [0, 99]);
        $f->addText('name', 'Jméno')->setRequired(true);
        $f->addText('surname', 'Příjmení')->setRequired(true);
        $f->addText('secondaryNumber', 'Alt. číslo');

        $f->addSubmit('submit', 'Přidat');

        $f->onSuccess[] = function (Form $form, ArrayHash $values) {
            $p = new Player();
            $p->assign($values);
            $p->teamInfo = $this->TIR->get($this->getParameter('team'));
            $this->PR->persist($p);
        };


        return $f;
    }

    private function addTeamInfoEditableText(Grid $g, $identifier)
    {

        return $g->addColumnText($identifier, $this::TEAM_INFO_GRID_LABELS[$identifier])
            ->setEditableCallback(GridHelpers::getEditableCallback($identifier, $this->TIR));
    }

    private function addPlayerEditableText(Grid $g, $identifier, $label)
    {

        return $g->addColumnText($identifier, $label)
            ->setEditableCallback(GridHelpers::getEditableCallback($identifier, $this->PR));
    }

    protected function createComponentTeamRosterAdministrationComponent()
    {
        return $this->TRACF->create();
    }


}