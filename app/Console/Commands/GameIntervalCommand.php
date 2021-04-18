<?php

namespace App\Console\Commands;

use App\Enums\Statuses;
use App\Events\GameIntervalEvent;
use App\Http\Controllers\GameController;
use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GameIntervalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:interval';

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
        for($i = 0; $i < 60; $i += 5) {
            echo "Checking intervals\n";

            $now = Carbon::now();

            foreach (Game::all() as $game) {
                if ($game->status == Statuses::Ongoing) {
                    if (!array_key_exists($game->id, $this->lastUpdates)) {
                        $this->lastUpdates[$game->id] = $now;
                    }

                    $difference = $now->diffInSeconds($this->lastUpdates[$game->id]);
                    echo "Difference: " . $difference . "\n";

                    if ($difference >= $game->interval) {
                        event(new GameIntervalEvent($game->id, new GameController()));
                        $this->lastUpdates[$game->id] = $now;
                        echo "Invoking interval of game " . $game->id . "\n";
                    }
                }
            }

            sleep(5);
        }
        return 0;
    }
}
