<?php

namespace Minicup\AdminModule\Presenters;


use Grido\Components\Columns\Column;
use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Components\IMatchFormComponentFactory;
use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\TeamInfoRepository;

class TeamPresenter extends BaseAdminPresenter
{
    /** @var IMatchFormComponentFactory @inject */
    public $MFCF;

    /** @var Connection @inject */
    public $connection;

    /** @var TeamInfoRepository @inject */
    public $TIR;

    public function renderList(Category $category)
    {
        $this->template->category = $category;
    }

    /***/
    public function createComponentMatchFormComponent()
    {
        return $this->MFCF->create($this->params['category'], 5);
    }

    /***/
    public function createComponentMatchesGridComponent($name)
    {
        $connection = $this->connection;
        $TIR = $this->TIR;
        $g = new Grid($this, $name);

        $f = $connection->select('[ti].*')->from('[team_info]')->as('ti')->where('ti.[category_id] = ', $this->getParameter('category')->id);
        $g->setModel($f);
        $g->addColumnNumber('id', '#');
        $g->addColumnText('name', 'Název')->setEditableCallback(function ($id, $newValue, $oldValue, Column $column) use ($TIR, $g) {
            $homeTeam = $TIR->get($id);
            $homeTeam->name = $newValue;
            $TIR->persist($homeTeam);
            $g->reload();
            return TRUE;
        });
        $g->addColumnText('slug', 'Slug')->setEditableCallback(function ($id, $newValue, $oldValue, Column $column) use ($TIR, $g) {
            $homeTeam = $TIR->get($id);
            $homeTeam->slug = $newValue;
            $TIR->persist($homeTeam);
            $g->reload();
            return TRUE;
        });
        return $g;
    }


}