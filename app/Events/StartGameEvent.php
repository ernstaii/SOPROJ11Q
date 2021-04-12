<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StartGameEvent implements ShouldBroadcastNow {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $gameId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(int $gameId){
        $this->gameId = $gameId;
    }

    /**
     * Get the channels the event should broadcast on.
     * Uses a public channel named "game.$gameId"
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(){
        return new Channel('game.'.$this->gameId);
    }

    /**
     * Gets the name of this event on the socket connection
     *
     * @return string
     */
    public function broadcastAs(){
        return 'startGame';
    }
}
