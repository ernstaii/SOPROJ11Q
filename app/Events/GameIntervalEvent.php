<?php

namespace App\Events;

use App\Http\Controllers\GameController;

class GameIntervalEvent extends GameEvent
{
    public $users;

    public function __construct($gameId, $users)
    {
        parent::__construct($gameId);
        $this->users = $users;
    }


    public function broadcastAs()
    {
        return 'game.interval';
    }
}
