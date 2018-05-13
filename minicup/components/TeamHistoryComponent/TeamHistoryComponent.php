<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Team;
use Minicup\Model\Manager\TeamHistoryManager;
use Minicup\Model\Manager\TeamHistoryRecord;
use Minicup\Model\Repository\TeamRepository;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;

interface ITeamHistoryComponentFactory
{
    /**
     * @param Team $team
     * @return TeamHistoryComponent
     */
    public function create(Team $team);

}

class TeamHistoryComponent extends BaseComponent
{
    /** @var Team $team */
    private $team;

    /** @var TeamRepository */
    private $TR;

    /**  @var TeamHistoryManager */
    private $teamHistoryManager;

    /** @var Cache */
    private $cache;

    public function __construct(Team $team,
                                TeamRepository $TR,
                                TeamHistoryManager $teamHistoryManager,
                                IStorage $storage)
    {
        $this->team = $team;
        $this->TR = $TR;
        $this->teamHistoryManager = $teamHistoryManager;
        $this->cache = new Cache($storage);
        parent::__construct();
    }

    public function render()
    {
        $this->template->team = $this->team;
        parent::render();
    }

    public function handleData()
    {
        $this->presenter->sendJson($this->cache->load($this->team->i->getCacheTag(static::class), function (& $depends) {
            $depends[Cache::TAGS] = [$this->team->getCacheTag()];

            $data = ['labels' => [], 'series' => [[]]];
            $history = $this->teamHistoryManager->getSingleHistoryForTeam($this->team->i);
            $teamsInCategory = count($this->team->category->teams);
            foreach ($history as $record) {
                /** @var TeamHistoryRecord $record */
                $data['labels'][] = $record->againstTeam->name;
                $data['series'][0][] = $teamsInCategory + 1 - $record->team->order;
            }
            return $data;
        }));

    }
}