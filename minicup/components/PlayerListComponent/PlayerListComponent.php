<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Player;

interface IPlayerListComponentFactory
{
    /**
     * @param Player[] $players
     * @return PlayerListComponent
     */
    public function create(array $players);

}

class PlayerListComponent extends BaseComponent
{
    /** @var  Player[] */
    private $players;

    /**
     * @param Player[] $players
     */
    public function __construct(array $players)
    {
        parent::__construct();
        $this->players = $players;
    }

    public function render()
    {
        $this->template->players = $this->players;
        parent::render();
    }

}