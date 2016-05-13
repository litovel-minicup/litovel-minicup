<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;
use Minicup\Model\TeamHistoryManager;
use Minicup\Model\TeamHistoryRecord;

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

    /** @var TeamHistoryManager */
    private $teamHistoryManager;

    public function __construct(Category $category,
                                TeamHistoryManager $teamHistoryManager)
    {
        $this->category = $category;
        $this->teamHistoryManager = $teamHistoryManager;
        parent::__construct();
    }

    public function render()
    {
        $this->template->category = $this->category;
        parent::render();
    }

    public function handleData()
    {
        $history = $this->teamHistoryManager->getHistoryForTeams(array_map(function (Team $team) {
            return $team->i;
        }, $this->category->teams));
        $maxRecords = max(array_map(function ($line) {
            return count($line);
        }, $history));
        $teamsInCategory = count($this->category->teams);
        $data = ['labels' => range(1, $maxRecords), 'series' => []];
        /**
         * @var int                        $id team id
         * @var TeamHistoryRecord[]|NULL[] $teamLine
         */
        foreach ($history as $id => $teamLine) {
            $series = [];
            /** @var TeamHistoryRecord|NULL $record */
            foreach ($teamLine as $record) {
                $series[] = $record instanceof TeamHistoryRecord ? ($teamsInCategory + 1 - $record->order) : $record;
            }
            $data['series'][] = [
                'data' => $series,
                'name' => reset($teamLine)->team->name
            ];
        };
        $this->presenter->sendJson($data);
    }
}