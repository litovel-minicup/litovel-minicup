<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\TeamRepository;

class CategoryHistoryComponent extends BaseComponent
{
    /** @var Category $category */
    private $category;

    /** @var TeamRepository */
    private $TR;

    public function __construct(Category $category, TeamRepository $TR)
    {
        $this->category = $category;
        $this->TR = $TR;
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
        }, $this->category->teams));

        $data = array("labels" => range(1, $maxMatches), "series" => array());
        foreach ($this->category->teams as $team) {
            $series = array();
            /** @var Team $historyTeam */
            $historicalTeams = $this->TR->findHistoricalTeams($team);
            foreach (array_merge($historicalTeams, array($team)) as $historyTeam) {
                $series[] = count($this->category->teams) - $historyTeam->order;
            }
            if (count($series) < $maxMatches) {
                $lastOrder = array_slice($series, -1);
                for ($i=0; $i <= $maxMatches - count($series); $i++) {
                    $series[] = $lastOrder[0];
                }
            }
            $data['series'][] = array('data' => $series, 'name' => $team->i->name);

        }
        //dump($data);
        $this->presenter->sendJson($data);
    }
}

interface ICategoryHistoryComponentFactory
{
    /**
     * @param Category $category
     * @return CategoryHistoryComponent
     */
    public function create(Category $category);

}