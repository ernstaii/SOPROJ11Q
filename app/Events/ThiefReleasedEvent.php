<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThiefReleasedEvent extends GameEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(User $user)
    {
        $this->gameId = $user->inviteKey->game->id;
        $this->message = 'Speler ' . $user->username . ' is zojuist vrijgelaten.';
    }

    public function broadcastAs()
    {
        return 'thief.released';
    }
}
