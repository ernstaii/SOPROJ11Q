<?php

namespace App\Events;

use App\Http\Controllers\GameController;

class GameIntervalEvent extends GameEvent
{
    private $gameController;

    public function __construct($gameId, $gameController)
    {
        parent::__construct($gameId);
        $this->gameController = $gameController;
    }


    public function broadcastAs()
    {
        return 'game.interval';
    }

    public function boardcastWith()
    {
        $data = [];
        foreach($this->gameController->getUsersInGame($this->gameId) as $user){
            array_push($data["users"], [
                "id" => $user->id,
                "location" => $user->location
            ]);
        }
        return $data;
    }
}
