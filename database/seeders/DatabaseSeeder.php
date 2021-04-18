<?php

namespace Database\Seeders;

use App\Models\Loot;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            GamesTableSeeder::class,
            InviteKeysTableSeeder::class,
            UsersTableSeeder::class,
        ]);

        Loot::factory(10)->create();
        $this->call(LootsTableSeeder::class);
    }
}
