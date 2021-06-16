<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerJoinedGameEvent extends GameEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public function __construct($gameId, User $user)
    {
        parent::__construct($gameId);

        $fullUser = $user->toArray();
        $fullUser["role"] = $user->inviteKey->role;
        $this->user = $fullUser;
        $this->message = 'Speler ' . $user->username . ' is tot het spel toegetreden.';
    }

    public function broadcastAs()
    {
        return 'player.joined';
    }
}
