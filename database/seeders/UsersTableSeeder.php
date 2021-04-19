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

        User::query()->create([
            'username' => 'Willem',
            'location' => '51.498134,-0.201755',
            'invite_key' => 'A0B3',
            'game_id' => $id,
            'role' => Roles::Thief,
        ]);

        User::query()->create([
            'username' => 'Bart',
            'location' => '51.498134,-0.201755',
            'invite_key' => 'LKJ3',
            'game_id' => $id,
            'role' => Roles::Police,
        ]);

        $id = Game::all()->last()->id;

        User::query()->create([
            'username' => 'Johan',
            'location' => '51.498134,-0.201755',
            'invite_key' => 'BF3V',
            'game_id' => $id,
            'role' => Roles::Police,
        ]);

        User::query()->create([
            'username' => 'Rik',
            'location' => '51.498134,-0.201755',
            'invite_key' => 'FF3Q',
            'game_id' => $id,
            'role' => Roles::Thief,
        ]);
    }
}
