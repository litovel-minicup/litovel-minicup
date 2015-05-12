<?php

namespace Minicup\AdminModule\Presenters;


use Grido\Components\Columns\Column;
use Grido\Components\Filters\Filter;
use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Components\IMatchFormComponentFactory;
use Minicup\Components\MatchFormComponent;
use Minicup\Model\Entity\Category;
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
        $MR = $this->MR;
        $TIR = $this->TIR;
        $g = new Grid();
        $g->setFilterRenderType(Filter::RENDER_INNER);
        $f = $connection->select('[m].*')->from('[match]')->as('m')->where('m.[category_id] = ', $this->getParameter('category')->id);
        $f->leftJoin('[team_info]')->as('hti')->on('m.[home_team_info_id] = hti.[id]')->select('hti.[name] htiname');
        $f->leftJoin('[team_info]')->as('ati')->on('m.[away_team_info_id] = ati.[id]')->select('ati.[name] atiname');
        // TODO: add score editing
        $g->setModel($f);
        $g->addColumnNumber('id', '#');
        $g->addColumnText('htiname', 'Domácí')->setEditableCallback(function ($id, $newValue, $oldValue, Column $column) use ($MR, $TIR, $g) {
            $homeTeamId = $MR->get($id)->homeTeam->id;
            $homeTeam = $TIR->get($homeTeamId);
            $homeTeam->name = $newValue;
            $TIR->persist($homeTeam);
            return TRUE;
        });
        $g->addColumnText('atiname', 'Hosté');
        return $g;
    }
}