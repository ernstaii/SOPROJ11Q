<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\InviteKey;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Game 1
        User::query()->create([
            'username' => 'Willem',
            'location' => '51.498134,-0.201755',
            'invite_key' => 'A0B3',
            'game_id' => 1,
            'role' => Roles::Thief,
        ]);

        User::query()->create([
            'username' => 'Rik',
            'location' => '51.498134,-0.201755',
            'invite_key' => 'FF3Q',
            'game_id' => 2,
            'role' => Roles::Thief,
        ]);

        // Game 2
        User::query()->create([
            'username' => 'Bart',
            'location' => '51.498134,-0.201755',
            'invite_key' => 'LKJ3',
            'game_id' => 1,
            'role' => Roles::Police,
        ]);

        User::query()->create([
            'username' => 'Johan',
            'location' => '51.498134,-0.201755',
            'invite_key' => 'BF3V',
            'game_id' => 2,
            'role' => Roles::Police,
        ]);
    }
}
