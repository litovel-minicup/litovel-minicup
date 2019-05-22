<?php

namespace Minicup\AdminModule\Presenters;


use Dibi\Row;
use Grido\Components\Columns\Column;
use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Components\IMatchFormComponentFactory;
use Minicup\Components\ITeamRosterManagementComponentFactory;
use Minicup\Components\MatchFormComponent;
use Minicup\Misc\GridHelpers;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Player;
use Minicup\Model\Repository\PlayerRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;

class TeamPresenter extends BaseAdminPresenter
{

    /**
     * Mapp db name => Real name
     */
    const TEAM_INFO_GRID_LABELS = [
        'id' => '#',
        'name' => 'Název',
        'slug' => 'Slug',
        'abbr' => 'Zkratka',
        'trainer_name' => 'Trenér',
        'dress_color' => 'Barva dresu',
        'dress_color_secondary' => 'Sek. barva dresu',

        'color_primary' => 'Prim. barva',
        'color_secondary' => 'Sek. barva',
        'color_text' => 'Tex. barva',
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

    public function renderRosterExport(Category $category)
    {
        $this->template->category = $category;
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
    public function createComponentTeamsGridComponent($name)
    {
        $connection = $this->connection;
        $TIR = $this->TIR;
        $CR = $this->CR;

        $that = $this;
        $g = new Grid();

        $f = $connection->select('[ti].*')
            ->from('[team_info]')->as('ti')
            ->where('ti.[category_id] = ', $this->getParameter('category')->id)
            ->select('COUNT(DISTINCT [photo_tag.photo_id]) as photo_count')
            ->select('COUNT(DISTINCT [player.id]) as player_count')
            ->leftJoin('[photo_tag]')->on('[photo_tag.tag_id] = [ti.tag_id]')
            ->leftJoin('[player]')->on('[player.team_info_id] = [ti.id]')
            ->groupBy('[ti.id]');

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
        $this->addTeamInfoEditableText($g, 'name', 'name');
        $this->addTeamInfoEditableText($g, 'slug', 'slug');
        $this->addTeamInfoEditableText($g, 'abbr', 'abbr');

        // Trainer name
        $this->addTeamInfoEditableText($g, 'trainerName', 'trainer_name');

        // Team color
        $this->addColorEditableText($g, 'colorPrimary', 'color_primary');
        $this->addColorEditableText($g, 'colorSecondary', 'color_secondary');
        $this->addColorEditableText($g, 'colorText', 'color_text');

        // Dress color
        $this->addTeamInfoEditableText($g, 'dressColor', 'dress_color');
        $this->addTeamInfoEditableText($g, 'dressColorSecondary', 'dress_color_secondary');

        $this->addColorColumn($g, 'dress_color_min', 'dressColorMin', 'Prim. barva od');
        $this->addColorColumn($g, 'dress_color_max', 'dressColorMax', 'Prim. barva do');
        $this->addColorColumn($g, 'dress_color_secondary_min', 'dressColorSecondaryMin', 'Sek. barva od');
        $this->addColorColumn($g, 'dress_color_secondary_max', 'dressColorSecondaryMax', 'Sek. barva do');

        $g->addColumnNumber('photo_count', 'Fotek');
        $g->addColumnNumber('player_count', 'Hráčů');

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

    private function addColorColumn(Grid $g, string $column, string $identifier, string $label)
    {
        $color = new TextInput('color');
        $color->setType('text');
        $g
            ->addColumnText($column, $label)
            ->setEditableControl($color)
            ->setEditableCallback(function ($id, $newValue, $oldValue, Column $column) use ($identifier) {
                $entity = $this->TIR->get($id, FALSE);
                /* $hsv = ColorUtils::rgbToHsv(
                    hexdec(substr($newValue, 1, 2)),
                    hexdec(substr($newValue, 1 + 2, 2)),
                    hexdec(substr($newValue, 1 + 2 + 2, 2))
                );
                Debugger::barDump($hsv, 'HSV');
                $hsv[1] = $hsv[2] = 100;
                $rgb = ColorUtils::hsvToRgb(...$hsv);
                Debugger::barDump($hsv, 'RGB');
                $pad = function($c) {
                    return str_pad(dechex($c), 2, '0', STR_PAD_LEFT);
                };
                $entity->{$identifier} = "#" . $pad($rgb[0]) . $pad($rgb[1]) . $pad($rgb[2]);
                Debugger::barDump($entity->{$identifier}, 'conv');
                */

                $entity->{$identifier} = $newValue;
                $this->TIR->persist($entity);
                return TRUE;
            })
            ->setCellCallback(function (Row $row, Html $td) use ($column) {
                $td->addAttributes(['class' => ["grid-cell-$column"], 'style' => "background-color: hsl({$row->{$column}}, 100%, 50%);"]);

                return $td;
            });
    }

    private function addColorEditableText(Grid $g, string $identifier, string $column)
    {
        return $this->addTeamInfoEditableText($g, $identifier, $column)->setCellCallback(
            function (Row $row, Html $td) use ($column) {
                return $td->addAttributes([
                    'class' => ["grid-cell-$column"],
                    'style' => "background-color: {$row->{$column}}; text-align: center;"
                ]);
            }
        );
    }

    private function addTeamInfoEditableText(Grid $g, $identifier, $column)
    {

        return $g->addColumnText($column, $this::TEAM_INFO_GRID_LABELS[$column])
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