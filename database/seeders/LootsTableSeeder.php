<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Loot;
use Illuminate\Database\Seeder;

class LootsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $games = Game::all();
        $loots = Loot::all();

        foreach ($loots as $loot) {
            $loot->games()->attach(
                $games->find(rand(1, 2))
            );
        }
    }
}
