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
    private $smokescreened_users;

    public function __construct()
    {
        parent::__construct();
        $this->smokescreened_users = new Collection();
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
                        $this->log('    Active users: ' . json_encode($active_users));
                        $active_users = $this->check_active_smokescreens($active_users);
                        $this->log('    Game ID: ' . $game->id);
                        $this->log('    Active users: ' . json_encode($active_users));
                        $this->log('    Game loot: ' . json_encode($game->loot));
                        $this->log('    Drone is active: ' . $this->drone_is_active($active_users));
                        event(new GameIntervalEvent($game->id, $active_users, $game->loot, $this->drone_is_active($active_users), $game->time_left, $this->smokescreened_users));
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
        $this->log('    Checking if drones are active...');
        $drone_activated = 0;
        $this->log('    drone_activated: ' . strval($drone_activated));
        foreach ($users as $user) {
            $this->log('      Checking user: ' . $user->username);
            if ($user->gadgets->count() > 0) {
                foreach ($user->gadgets as $gadget) {
                    $this->log('        Checking gadget: ' . $gadget->name);
                    if ($gadget->pivot->in_use && $gadget->name === Gadgets::Drone) {
                        $this->log('        Active drone found');
                        $gadget->pivot->in_use = null;
                        $gadget->pivot->location = null;
                        $gadget->pivot->activated_at = null;
                        $gadget->pivot->save();
                        $drone_activated = 1;
                    }
                }
            }
        }

        $this->log('    drone_activated: ' . strval($drone_activated));
        return ($drone_activated >= 1);
    }

    private function check_active_smokescreens(Collection $users)
    {
        $this->log('    Checking if smokescreens are active...');
        $users_with_smokescreens = [];
        for ($i = 0; $i < $users->count(); $i++) {
            $this->log('      Checking user: ' . $users[$i]->username);
            if ($users[$i]->gadgets->count() > 0) {
                $this->log('      Gadgets: ' . json_encode($users[$i]->gadgets));
                foreach ($users[$i]->gadgets as $gadget) {
                    $this->log('        Checking gadget: ' . $gadget->name);
                    if ($gadget->pivot->in_use && $gadget->name === Gadgets::Smokescreen) {
                        $gadget->pivot->in_use = null;
                        $gadget->pivot->location = null;
                        $gadget->pivot->activated_at = null;
                        $gadget->pivot->save();
                        $this->smokescreened_users->push($users[$i]);
                        array_push($users_with_smokescreens, $i);
                    }
                }
            }
        }

        foreach ($users_with_smokescreens as $i) {
            $users->splice($i, 1);
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
