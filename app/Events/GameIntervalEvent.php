<?php

namespace App\Events;

class GameIntervalEvent extends GameEvent
{
    public $users;
    public $loot;
    public $drone_is_active;

    public function __construct($gameId, $users, $loot, $drone_is_active)
    {
        parent::__construct($gameId);
        $this->users = $users;
        $this->loot = $loot;
        $this->drone_is_active = $drone_is_active;
    }

    public function broadcastAs()
    {
        return 'game.interval';
    }
}
