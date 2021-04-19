<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Loot;
use Illuminate\Database\Seeder;

class LootsTableSeeder extends Seeder
{
    public function run()
    {
        $name = ['Goldbar', 'Rabobank', 'FEBO Muur', 'Albert Heijn', 'Jumbo', 'Spar', 'Co-op', 'Avans Hogeschool', 'Kees Kroket', 'Subway'];
        $games = Game::all();

        for ($i = 0; $i < 10; $i++) {
            $latitude = "51.7" . strval(rand(43866, 79043));
            $longitude = "5." . strval(rand(491387, 553818));
            Game::find((rand(0, 1) == 0) ? $games->first()->id : $games->last()->id)->loots()->attach([
                'name' => $name[$i],
                'location' => $latitude . "," . $longitude
            ]);
        }
    }
}
