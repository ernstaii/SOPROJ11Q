<?php

namespace App\Events;

class GameIntervalEvent extends GameEvent
{
    public $users;
    public $loot;

    public function __construct($gameId, $users, $loot)
    {
        parent::__construct($gameId);

        $this->users = [];
        foreach ($users as $user){
            array_push($this->users, $user);
        }
        
        $this->loot = $loot;
    }

    public function broadcastAs()
    {
        return 'game.interval';
    }
}
