<?php

namespace App\Console\Commands;

use App\Enums\Statuses;
use App\Events\EndGameEvent;
use App\Events\GameIntervalEvent;
use App\Http\Controllers\GameController;
use App\Models\Game;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class GameIntervalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:intervals {--log}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Takes care of pushing game interval events';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $gameController = new GameController();
        $lastUpdates = [];

        while (true) {
            $game_ended = false;
            $this->log("Checking intervals");

            $now = Carbon::now();

            try {
                foreach (Game::all() as $game) {
                    $gameTimeExpired = $now->diffInHours($game->updated_at) > $game->duration / 60 || $game->time_left <= 0;

                    if ($game->status == Statuses::Ongoing && !$gameTimeExpired) {
                        if (!array_key_exists($game->id, $lastUpdates)) {
                            $lastUpdates[$game->id] = $now;
                        }
                        $game->time_left = $game->time_left - $now->diffInSeconds(Carbon::parse($game->updated_at));

                        if ($game->time_left <= 0)
                            $game_ended = true;

                        $difference = $now->diffInSeconds($lastUpdates[$game->id]);
                        $this->log("  Game " . $game->id . " time difference: " . $difference . "/" . $game->interval);

                        if ($difference >= $game->interval || $game_ended) {
                            $users = $gameController->getUsers($game);
                            event(new GameIntervalEvent($game->id, $users));
                            $lastUpdates[$game->id] = $now;
                            $this->log("    Invoking interval of game " . $game->id);

                            if ($game_ended) {
                                $game->status = Statuses::Finished;
                                event(new EndGameEvent($game->id, 'De tijd is op. Het spel is beëindigd.'));
                            }

                            $game->save();
                        }
                    } else if (array_key_exists($game->id, $lastUpdates)) {
                        // Remove unused time stamps so intervals won't be instant when an id is reused or a game is resumed
                        unset($lastUpdates[$game->id]);
                    }
                }
            } catch (Exception $exception) {
                echo "An error occurred: \n\r" . $exception->getTraceAsString() . "\n\r";
            }

            sleep(5);
        }
    }

    private function log($message)
    {
        if ($this->option('log')) {
            echo $message . "\n";
        }
    }
}
