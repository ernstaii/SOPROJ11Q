<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $id = Game::all()->first()->id;

        $latitude = "51.7" . strval(rand(43866, 79043));
        $longitude = "5." . strval(rand(491387, 553818));

        User::query()->create([
            'username' => 'Willem',
            'location' => $latitude . ',' . $longitude,
            'invite_key' => 'A0B3',
            'game_id' => $id,
            'role' => Roles::Thief,
        ]);

        $latitude = "51.7" . strval(rand(43866, 79043));
        $longitude = "5." . strval(rand(491387, 553818));

        User::query()->create([
            'username' => 'Bart',
            'location' => $latitude . ',' . $longitude,
            'invite_key' => 'LKJ3',
            'game_id' => $id,
            'role' => Roles::Police,
        ]);

        $id = Game::all()->last()->id;

        $latitude = "51.7" . strval(rand(43866, 79043));
        $longitude = "5." . strval(rand(491387, 553818));

        User::query()->create([
            'username' => 'Johan',
            'location' => $latitude . ',' . $longitude,
            'invite_key' => 'BF3V',
            'game_id' => $id,
            'role' => Roles::Police,
        ]);

        $latitude = "51.7" . strval(rand(43866, 79043));
        $longitude = "5." . strval(rand(491387, 553818));

        User::query()->create([
            'username' => 'Rik',
            'location' => $latitude . ',' . $longitude,
            'invite_key' => 'FF3Q',
            'game_id' => $id,
            'role' => Roles::Thief,
        ]);
    }
}
