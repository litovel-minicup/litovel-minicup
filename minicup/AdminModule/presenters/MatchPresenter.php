<?php

namespace Minicup\AdminModule\Presenters;


use Grido\Components\Columns\Column;
use Grido\Components\Filters\Filter;
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

    /**
     * @return MatchFormComponent
     */
    public function createComponentMatchFormComponent()
    {
        return $this->MFCF->create($this->params['category'], 16);
    }

    /**
     * @return Grid
     */
    public function createComponentMatchesGridComponent()
    {
        $connection = $this->connection;
        $presenter = $this;
        $MM = $this->MM;
        $MR = $this->MR;
        $TIR = $this->TIR;
        $g = new Grid();
        $g->setFilterRenderType(Filter::RENDER_INNER);
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
        $g->addColumnText('score_home', 'Skóre domácích')->setEditableCallback(
            function ($id, $newValue, $oldValue, Column $column) use ($MR, $MM) {
                /** @var Match $match */
                $match = $MR->get($id);
                if ($match->confirmed === NULL) {
                    return FALSE;
                }
                $match->scoreHome = $newValue;
                $MR->persist($match);
                $MM->regenerateFromMatch($match);
                return TRUE;
            });
        $g->addColumnText('score_away', 'Skóre hostů')->setEditableCallback(
            function ($id, $newValue, $oldValue, Column $column) use ($MR, $MM) {
                /** @var Match $match */
                $match = $MR->get($id);
                $match->scoreAway = $newValue;
                if ($match->confirmed === NULL) {
                    return FALSE;
                }
                $MR->persist($match);
                $MM->regenerateFromMatch($match);
                return TRUE;
            });
        $g->addColumnText('atiname', 'Hosté');
        return $g;
    }
}