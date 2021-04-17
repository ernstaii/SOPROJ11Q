<?php

namespace App\Events;

class StartGameEvent extends GameEvent {

    public function broadcastAs():string {
        return 'game.start';
    }
}
