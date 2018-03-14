<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;
use Minicup\Model\Manager\TeamHistoryManager;
use Minicup\Model\Manager\TeamHistoryRecord;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;

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

    /** @var Cache */
    private $cache;

    public function __construct(Category $category,
                                TeamHistoryManager $teamHistoryManager,
                                IStorage $storage)
    {
        $this->category = $category;
        $this->teamHistoryManager = $teamHistoryManager;
        $this->cache = new Cache($storage);
        parent::__construct();
    }

    public function render()
    {
        $this->template->category = $this->category;
        parent::render();
    }

    public function handleData()
    {
        $this->presenter->sendJson($this->cache->load($this->category->getCacheTag(static::class), function (& $depends) {
            $depends[Cache::TAGS] = [$this->category->getCacheTag()];

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
                if (!reset($teamLine)) {
                    continue;
                }
                $series = [];
                /** @var TeamHistoryRecord|NULL $record */
                foreach ($teamLine as $record) {
                    $series[] = $record instanceof TeamHistoryRecord ? ($teamsInCategory + 1 - $record->order) : $record;
                }
                $data['series'][] = [
                    'data' => $series,
                    'name' => reset($teamLine) ? reset($teamLine)->team->name : ' '
                ];
            };
            return $data;
        }));
    }
}