<?php

namespace Minicup\AdminModule\Presenters;


use Grido\Components\Columns\Column;
use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Components\IMatchFormComponentFactory;
use Minicup\Components\MatchFormComponent;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Manager\MatchManager;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\TeamInfoRepository;

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
     */
    public function createComponentMatchesGridComponent($name)
    {
        $connection = $this->connection;
        $MM = $this->MM;
        $MR = $this->MR;
        $g = new Grid($this, $name);
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

        $g->addColumnText('htiname', 'Domácí');

        $editCallback = function ($id, $newValue, $oldValue, Column $column) use ($MR, $MM) {
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

        $g->addColumnText('scoreHome', 'Skóre domácích')
            ->setEditableCallback($editCallback)
            ->setColumn('score_home');

        $g->addColumnText('match_term', 'Čas')->setCustomRender(function ($row) use ($MR) {
            /** @var Match $match */
            $match = $MR->get($row->id);
            return $match->matchTerm->day->day->format('j. n.') . ' ' . $match->matchTerm->start->format('G:i');
        });
        $g->addColumnText('scoreAway', 'Skóre hostů')
            ->setEditableCallback($editCallback)
            ->setColumn('score_away');

        $g->addColumnText('atiname', 'Hosté');
        return $g;
    }
}