<?php


namespace App\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class GameEvent implements ShouldBroadcastNow {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $gameId;

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
     * @return Channel
     */
    public function broadcastOn():Channel {
        return new Channel('game.'.$this->gameId);
    }

    /**
     * Gets the name of this event on the socket connection
     *
     * @return string
     */
    public abstract function broadcastAs():string;
}
