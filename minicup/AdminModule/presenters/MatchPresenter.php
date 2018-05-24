<?php

namespace Minicup\AdminModule\Presenters;


use Dibi\Row;
use Grido\Components\Columns\Column;
use Grido\Components\Columns\Date;
use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Components\IMatchFormComponentFactory;
use Minicup\Components\MatchFormComponent;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Manager\MatchManager;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Nette\Forms\Controls\SelectBox;
use Nette\Utils\Html;

final class MatchPresenter extends BaseAdminPresenter
{
    /** @var IMatchFormComponentFactory @inject */
    public $MFCF;

    /** @var Connection @inject */
    public $connection;

    /** @var MatchRepository @inject */
    public $MR;

    /** @var TeamInfoRepository @inject */
    public $TIR;

    /** @var MatchManager @inject */
    public $MM;

    public function renderConfirm(Category $category)
    {
        $this->template->category = $category;
    }

    public function renderList(Category $category)
    {
        $this->template->category = $category;
    }

    public function renderTable(Category $category)
    {
        $this->template->teams = $category->teamInfos;
        $data = [];
        /** @var TeamInfo $team1 */
        foreach ($this->template->teams as $team1) {
            $row = [];
            /** @var TeamInfo $team2 */
            foreach ($this->template->teams as $team2) {
                $row[] = $this->MR->getCommonMatchForTeams(
                    $team1->team,
                    $team2->team,
                    NULL
                );
            }
            $data[] = $row;
        }
        $this->template->data = $data;
    }

    public function renderSchedule(Category $category)
    {
        $this->template->days = $this->MR->groupMatchesByDay($category);
    }

    public function renderCategory(Category $category)
    {
        $this->template->year = $category->year;
    }

    /**
     * @return MatchFormComponent
     */
    public function createComponentMatchFormComponent()
    {
        return $this->MFCF->create($this->params['category'], 8);
    }

    /**
     * @param string $name
     * @return Grid
     * @throws \Grido\Exception
     */
    public function createComponentMatchesGridComponent($name)
    {
        $connection = $this->connection;
        $MM = $this->MM;
        $MR = $this->MR;
        $g = new Grid();
        $f = $connection->select('[m.*]')
            ->from('[match] m')
            ->where('m.[category_id] = ', $this->getParameter('category')->id)
            ->orderBy('d.[day] ASC, mt.[start] ASC, m.[id] ASC');
        $f->leftJoin('[team_info] hti')->on('m.[home_team_info_id] = hti.[id]')->select('hti.[name] htiname');
        $f->leftJoin('[team_info] ati')->on('m.[away_team_info_id] = ati.[id]')->select('ati.[name] atiname');
        $f->leftJoin('[match_term] mt')->on('m.[match_term_id] = mt.[id]');
        $f->leftJoin('[day] d')->on('mt.[day_id] = d.[id]');
        $g->setModel($f);

        $g->addColumnNumber('id', '#');

        $scoreEditCallback = function ($id, $newValue, $oldValue, Column $column) use ($MR, $MM) {
            /** @var Match $match */
            $match = $MR->get($id);
            $match->{$column->getName()} = $newValue;
            if ($match->confirmed === NULL) {
                return FALSE;
            }
            $MR->persist($match);
            $count = $MM->regenerateFromMatch($match);
            $this->flashMessage("Skóre bylo úspěšně upraveno, historie byla přegenerována pro {$count} zápasů.");
            return TRUE;
        };


        $g->addColumnText('match_term', 'Čas')->setCustomRender(function ($row) use ($MR) {
            /** @var Match $match */
            $match = $MR->get($row->id);
            return $match->matchTerm->day->day->format('j. n.') . ' ' . $match->matchTerm->start->format('G:i');
        });

        $g->addColumnText('htiname', 'Domácí');
        $g->addColumnText('atiname', 'Hosté');

        $g->addColumnNumber('scoreHome', 'Skóre domácích')
            ->setEditableCallback($scoreEditCallback)
            ->setColumn('score_home');
        $g->addColumnNumber('scoreAway', 'Skóre hostů')
            ->setEditableCallback($scoreEditCallback)
            ->setColumn('score_away');

        $control = new SelectBox(NULL, Match::ONLINE_STATE_CHOICES);
        $g->addColumnText('online_state', 'Stav online')->setEditableControl($control)->setEditableCallback(function ($id, $new, $old, $column) {
            /** @var Match $match */
            $match = $this->MR->get($id, False);
            $match->onlineState = $new;
            $this->MR->persist($match);
            $this->flashMessage("Online stav zápasu {$id} změněn z {$old} na {$new}.");
            return TRUE;
        })->setCustomRender(function ($row) {
            return Match::ONLINE_STATE_CHOICES[$row->online_state];
        });
        $g->addColumnDate('first_half_start', 'Začátek první půle', Date::FORMAT_DATETIME);
        $g->addColumnDate('second_half_start', 'Začátek druhé půle', Date::FORMAT_DATETIME);
        $g->addColumnDate('confirmed', 'Potvrzeno', Date::FORMAT_DATETIME);
        $g->addColumnNumber('confirmed_as', 'Potvrzeno jako');
        $g->addColumnText('facebook_video_id', 'ID Facebook streamu')->setEditableCallback(function ($id, $new, $old, $col) {
            /** @var Match $match */
            $match = $this->MR->get($id, False);
            $match->facebookVideoId = $new;
            $this->MR->persist($match);
            return true;
        });
        $g->setRowCallback(function (Row $row, Html $tr) {
            if ($row->confirmed !== NULL)
                $tr->class[] = 'success';
            if ($row->online_state == Match::END_ONLINE_STATE && $row->confirmed === NULL)
                $tr->class[] = 'warning';
            if ($row->online_state != Match::END_ONLINE_STATE && $row->online_state != Match::INIT_ONLINE_STATE)
                $tr->class[] = 'info';
            return $tr;
        });
        return $g;
    }
}