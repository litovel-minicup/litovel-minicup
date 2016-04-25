<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\TeamRepository;

interface ICategoryHistoryComponentFactory
{
    /**
     * @param Category $category
     * @return CategoryHistoryComponent
     */
    public function create(Category $category);

}

class CategoryHistoryComponent extends BaseComponent
{
    /** @var Category $category */
    private $category;

    /** @var TeamRepository */
    private $TR;

    public function __construct(Category $category,
                                TeamRepository $TR)
    {
        $this->category = $category;
        $this->TR = $TR;
        parent::__construct();
    }

    public function render()
    {
        $this->template->category = $this->category;
        parent::render();
    }

    public function handleData()
    {
        $maxMatches = max(array_map(function (Team $team) {
                return count($team->getPlayedMatches());
            }, $this->category->teams)) + 1;

        $data = array('labels' => range(1, $maxMatches + 1), 'series' => array());
        $countOfTeams = count($this->category->teams);
        $n = 1;
        foreach ($this->category->teams as $team) {
            $series = array();
            /** @var Team $historyTeam */
            $historicalTeams = $this->TR->findHistoricalTeams($team);
            foreach (array_slice(array_merge($historicalTeams, array($team)), 1) as $historyTeam) {
                $series[] = $countOfTeams - $historyTeam->order;
            }
            if (count($series) < $maxMatches) {
                if (isset($lastOrder[0])) { // team has some order
                    $lastOrder = $lastOrder[0];
                } else {
                    $lastOrder = $countOfTeams - $n;
                }
                foreach (range(0, $maxMatches - count($series)) as $_) {
                    array_unshift($series, $lastOrder);
                }

            }
            $data['series'][] = array('data' => $series, 'name' => $team->i->name);
            $n++;
        }
        if ($this->presenter->isAjax()) {
            $this->presenter->sendJson($data);
        }
    }
}