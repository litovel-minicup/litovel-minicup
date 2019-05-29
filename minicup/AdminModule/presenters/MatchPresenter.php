<?php

namespace Minicup\AdminModule\Presenters;


use Dibi\Row;
use Grido\Components\Columns\Column;
use Grido\Components\Columns\Date;
use Grido\Grid;
use LeanMapper\Connection;
use LeanMapper\IMapper;
use Minicup\Components\IMatchFormComponentFactory;
use Minicup\Components\MatchFormComponent;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Manager\MatchManager;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Form;
use Nette\Utils\ArrayHash;
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

    /** @var IMapper @inject */
    public $mapper;

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
            ->orderBy('[d.day] ASC, [mt.start] ASC, [m.id] ASC');
        $f->innerJoin('[team_info]')->as('hti')->on('[m.home_team_info_id] = [hti.id]')->select('[hti.name]')->as('htiname');
        $f->innerJoin('[team_info]')->as('ati')->on('[m.away_team_info_id] = [ati.id]')->select('[ati.name]')->as('atiname');
        $f->innerJoin('[match_term]')->as('mt')->on('[m.match_term_id] = [mt.id]');
        $f->innerJoin('[day]')->as('d')->on('[mt.day_id] = [d.id]');
        $g->setModel($f);

        $g->addColumnNumber('id', '#');

        $g->addActionHref('slug', 'WEB')->setCustomHref(function ($row) {
            $match = $this->MR->get($row->id, FALSE);
            return $this->link(':Front:Match:detail', ['match' => $match]);
        });

        $g->addActionHref('liveStream', 'LIVE')->setCustomHref(function ($row) {
            $match = $this->MR->get($row->id, FALSE);
            return $this->link(':Admin:Match:liveStream', ['id' => $match->id]);
        });

        $scoreEditCallback = $this->getScoreEditableCallback();

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
        $g->addColumnText('online_state', 'Stav online')
            ->setEditableControl($control)
            ->setEditableRowCallback(function ($id, Column $col) {
                /** @var Match $match */
                $match = $this->MR->get($id, FALSE);
                return $match->getOnlineStateName();
            })
            ->setEditableCallback(function ($id, $new, $old, $column) {
                /** @var Match $match */
                $match = $this->MR->get($id, False);
                $match->onlineState = $new;
                $this->MR->persist($match);
                $this->flashMessage("Online stav zápasu {$id} změněn z {$old} na {$new}.");
                return TRUE;
            })
            ->setCustomRender(function ($row) {
                if ($row instanceof Row)
                    return Match::ONLINE_STATE_CHOICES[$row->online_state];
                return $row;
            });

        $halfStartEditableCallback = function ($id, $new, $old, Column $col) {
            /** @var Match $match */
            $match = $this->MR->get($id);
            $match->{$this->mapper->getEntityField('match', $col->getName())} = new DateTime($new);
            try {
                $this->MR->persist($match);
            } catch (\Exception $e) {
                $this->flashMessage("Chyba při editaci {$col->getName()} {$e->getMessage()}!");
                return TRUE;
            }

            $this->flashMessage("Sloupec {$col->getName()} úspěšně upraven!");
            return TRUE;
        };
        $g->addColumnDate('first_half_start', 'Začátek první půle', Date::FORMAT_DATETIME)
            ->setEditableCallback($halfStartEditableCallback);
        $g->addColumnDate('second_half_start', 'Začátek druhé půle', Date::FORMAT_DATETIME)
            ->setEditableCallback($halfStartEditableCallback);

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

    /**
     * @return \Closure
     */
    protected function getScoreEditableCallback(): \Closure
    {
        $scoreEditCallback = function ($id, $newValue, $oldValue, Column $column) {
            /** @var Match $match */
            $match = $this->MR->get($id);
            $match->{$column->getName()} = $newValue;
            if ($match->confirmed === NULL) {
                return FALSE;
            }
            $this->MR->persist($match);
            $count = $this->MM->regenerateFromMatch($match);
            $this->flashMessage("Skóre bylo úspěšně upraveno, historie byla přegenerována pro {$count} zápasů.");
            return TRUE;
        };
        return $scoreEditCallback;
    }
    /** @var Match */
    public $match;

    public function actionLiveStream($id)
    {
        $this->match = $this->MR->get($id);
    }

    public function renderLiveStream()
    {
        $this->template->match = $this->match;
    }

    public function createComponentLiveStreamForm()
    {
        $f = $this->formFactory->create();

        $f->addText('facebook_video_id')->setRequired(FALSE);
        $f->addText('youtube_video_id')->setRequired(FALSE);
        $f->addSubmit('submit', 'Uložit');
        $match = $this->match;
        $MR = $this->MR;
        $presenter = $this;
        $f->onSuccess[] = function (Form $f, ArrayHash $values) use ($match, $MR, $presenter) {
            $match->youtubeVideoId = $values->youtube_video_id;
            $match->facebookVideoId = $values->facebook_video_id;

            $MR->persist($match);
            $presenter->flashMessage('Úspěšně uloženo.');
            $presenter->redirect(':Admin:Match:liveStream', ['id' => $match->id]);
        };

        return $f;
    }
}