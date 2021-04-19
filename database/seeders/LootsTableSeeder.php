<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Loot;
use Illuminate\Database\Seeder;

class LootsTableSeeder extends Seeder
{
    public function run()
    {
        $games = Game::all();
        $loots = Loot::all();

        foreach ($loots as $loot) {
            $loot->games()->attach(
                $games->find(rand($games->first()->id, $games->last()->id))
            );
        }
    }
}
