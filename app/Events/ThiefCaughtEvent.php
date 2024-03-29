<?php

namespace App\Events;

use App\Enums\Gadgets;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ThiefCaughtEvent extends GameEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    private $smokescreened_users;

    public function __construct(User $user)
    {
        $this->smokescreened_users = new Collection();

        $fullUser = $user->toArray();
        $fullUser["role"] = $user->inviteKey->role;
        $this->user = $fullUser;

        $game = $user->get_game();
        $this->gameId = $game->id;
        $this->message = 'Boef ' . $user->username . ' is zojuist gevangen!';

        Notification::create([
            'game_id' => $game->id,
            'message' => $this->message
        ]);

        $users = new Collection($game->get_users_filtered_on_last_verified());
        $users = $this->check_active_smokescreens($users);
        event(new GameIntervalEvent($game->id, $users, $game->loot, $this->drone_is_active($users), $game->time_left, $this->smokescreened_users));
        $game->last_interval_at = Carbon::now();
        $game->save();
    }

    private function drone_is_active($users)
    {
        $drone_activated = false;
        foreach ($users as $user)
            if ($user->gadgets()->count() > 0)
                foreach ($user->gadgets() as $gadget)
                    if ($gadget->pivot->in_use && $gadget->name === Gadgets::Drone) {
                        $gadget->pivot->in_use = null;
                        $gadget->pivot->activated_at = null;
                        $gadget->pivot->save();
                        $drone_activated = true;
                    }

        return $drone_activated;
    }

    private function check_active_smokescreens(Collection $users)
    {
        for ($i = 0; $i < $users->count(); $i++) {
            if ($users[$i]->gadgets()->count() > 0) {
                $removed_user_count = 0;
                foreach ($users[$i]->gadgets() as $gadget)
                    if ($gadget->pivot->in_use && $gadget->name === Gadgets::Smokescreen) {
                        $gadget->pivot->in_use = null;
                        $gadget->pivot->activated_at = null;
                        $gadget->pivot->save();
                        $this->smokescreened_users->push($users[$i]);
                        $users->splice($i - $removed_user_count, 1);
                        $removed_user_count += 1;
                    }
            }
        }

        return $users;
    }

    public function broadcastAs()
    {
        return 'thief.caught';
    }
}
