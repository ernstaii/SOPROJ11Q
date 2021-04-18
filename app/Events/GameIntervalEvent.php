<?php

namespace App\Events;

use App\Http\Controllers\GameController;

class GameIntervalEvent extends GameEvent
{
    private $gameController;
    public $users;

    public function __construct($gameId, $gameController)
    {
        parent::__construct($gameId);
        $this->gameController = $gameController;
    }


    public function broadcastAs()
    {
        $this->users = [];
        foreach($this->gameController->getUsersInGame($this->gameId) as $user){
            array_push($this->users, [
                "id" => $user->id,
                "location" => $user->location
            ]);
        }

        return 'game.interval';
    }
}
