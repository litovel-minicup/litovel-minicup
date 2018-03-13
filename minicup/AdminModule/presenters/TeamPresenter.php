<?php

namespace Minicup\AdminModule\Presenters;


use Grido\Components\Columns\Column;
use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Components\IMatchFormComponentFactory;
use Minicup\Components\MatchFormComponent;
use Minicup\Misc\GridHelpers;
use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\TeamInfoRepository;

class TeamPresenter extends BaseAdminPresenter
{


    /**
     * Mapp db name => Real name
     */
    const LABELS = [
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

    /**
     * @param Category $category
     */
    public function renderList(Category $category)
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
    public function createComponentMatchesGridComponent($name)
    {
        $connection = $this->connection;
        $TIR = $this->TIR;
        $CR = $this->CR;
        $that = $this;
        $g = new Grid($this, $name);

        $f = $connection->select('[ti].*')
            ->from('[team_info]')->as('ti')
            ->where('ti.[category_id] = ', $this->getParameter('category')->id);

        $g->setModel($f);
        $g->addColumnNumber('id', $this::LABELS['id']);
        $g->addActionHref('slug', 'Detail na webu')->setCustomHref(function ($row) use ($CR, $that) {
            $category = $CR->get($row->category_id, FALSE);
            return $that->link(':Front:Team:detail', ['team' => $row->slug, 'category' => $category]);
        });

        // Name && slug
        $this->addEditableText($g, 'name');
        $this->addEditableText($g, 'slug');

        // Treiner name
        $this->addEditableText($g, 'trainer_name');


        // Dress color
        $this->addEditableText($g, 'dress_color');
        $this->addEditableText($g, 'dress_color_secondary');


        return $g;
    }

    /**
     * Add Column with Editable callback to griddo
     *
     * @param $g Grid instance
     * @param $identifier name of column. It must be mapped in LABELS
     *
     *
     */
    private function addEditableText($g, $identifier) {

        $g->addColumnText($identifier, $this::LABELS[$identifier])
            ->setEditableCallback(GridHelpers::getEditableCallback($identifier, $this->TIR));
    }


}