<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GamesTableSeeder extends Seeder
{
    public function run()
    {
        for ($i = 0; $i < 2; $i++) {
            Game::create();
        }
    }
}
