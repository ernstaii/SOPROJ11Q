<?php

namespace App\Console\Commands;

use App\Enums\Statuses;
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
    protected $lastUpdates = [];

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

        while(1<2){
            $this->log("Checking intervals");

            $now = Carbon::now();

            try {
                foreach (Game::all() as $game) {
                    $gameTimeExpired = $now->diffInHours($game->updated_at) > $game->duration / 60 || $game->time_left <= 0;
                    if ($game->status == Statuses::Ongoing && !$gameTimeExpired) {
                        if (!array_key_exists($game->id, $this->lastUpdates)) {
                            $this->lastUpdates[$game->id] = $now;
                        }

                        $difference = $now->diffInSeconds($this->lastUpdates[$game->id]);
                        $this->log("  Game " . $game->id . " time difference: " . $difference . "/" . $game->interval);

                        if ($difference >= $game->interval) {
                            $users = $gameController->getUsersInGame($game->id);
                            event(new GameIntervalEvent($game->id, $users));
                            $this->lastUpdates[$game->id] = $now;
                            $this->log("    Invoking interval of game " . $game->id);
                        }
                    }
                }
            }
            catch(Exception $exception) {
                echo "An error occurred: \n\r" . $exception->getTraceAsString() . "\n\r";
            }

            sleep(5);
        }
        return 0;
    }

    private function log($message){
        if($this->option('log')){
            echo $message . "\n";
        }
    }
}
