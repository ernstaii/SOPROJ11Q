<?php

namespace App\Console\Commands;

use App\Enums\Gadgets;
use App\Enums\Statuses;
use App\Enums\UserStatuses;
use App\Events\EndGameEvent;
use App\Events\GameIntervalEvent;
use App\Models\Game;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GameIntervalCommand extends Command
{
    protected $signature = 'game:interval {--log}';
    protected $description = 'Updates all on-going games';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->log('Interval started');
        $now = Carbon::now();

        try {
            $games = Game::where('status', '=', Statuses::Ongoing)->get();

            foreach ($games as $game) {
                $this->log('  Interval game ' . $game->id);
                $game_ended = $this->hasGameTimeElapsed($game, $now);

                if (!$game_ended) {
                    $this->log('    Game is on-going');
                    $difference = $now->diffInSeconds(Carbon::parse($game->last_interval_at ?? $game->started_at));
                    $this->log('    ' . $difference . ' seconds have elapsed since last interval');

                    if ($difference >= $game->interval) {
                        $this->log('    Invoking interval event');
                        $active_users = new Collection($game->get_users_filtered_on_last_verified());
                        $active_users = $this->check_active_smokescreens($active_users);
                        event(new GameIntervalEvent($game->id, $active_users, $game->loot, $this->drone_is_active($active_users)));
                        $this->check_alarms($game);
                        $game->last_interval_at = $now;
                    }
                } else {
                    $this->log('    Game has elapsed');
                    $game->status = Statuses::Finished;
                    $game->time_left = 0;

                    $users = $game->get_users();
                    foreach ($users as $user) {
                        $user->status = UserStatuses::Retired;
                        $user->save();
                    }

                    event(new EndGameEvent($game->id, 'De tijd is op. Het spel is beëindigd.'));
                }

                $game->save();
            }
        } catch (Exception $exception) {
            echo "An error occurred: \n\r" . $exception->getTraceAsString() . "\n\r";
        }
        $this->log('Interval ended');
        return 0;
    }

    private function drone_is_active(Collection $users)
    {
        $drone_activated = false;
        foreach ($users as $user)
            foreach ($user->gadgets() as $gadget)
                if ($gadget->pivot->in_use && $gadget->name === Gadgets::Drone) {
                    $gadget->pivot->in_use = null;
                    $gadget->pivot->location = null;
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
                        $gadget->pivot->location = null;
                        $gadget->pivot->activated_at = null;
                        $gadget->pivot->save();
                        $users->splice($i - $removed_user_count, 1);
                        $removed_user_count += 1;
                    }
            }
        }

        return $users;
    }

    private function hasGameTimeElapsed(Game $game, Carbon $now)
    {
        $game->time_left -= $now->diffInSeconds(Carbon::parse($game->updated_at));
        if ($game->time_left <= 0)
            return true;
        return false;
    }

    private function log($message)
    {
        if ($this->option('log')) {
            Log::debug($message);
        }
    }
}
