<?php

namespace App\Events;

class GameIntervalEvent extends GameEvent
{
    public $users;
    public $loot;

    public function __construct($gameId, $users, $loot)
    {
        parent::__construct($gameId);
        $this->users = $users;
        $this->loot = $loot;
    }

    public function broadcastAs()
    {
        return 'game.interval';
    }
}
