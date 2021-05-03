<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThiefReleasedEvent extends GameEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->gameId = $user->inviteKey->game->id;
        $this->user = $user;
    }

    public function broadcastAs()
    {
        return 'thief.released';
    }
}
