<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(ConstantGameSeeder::class);
        $this->call(RandomGameSeeder::class);
        $this->call(GadgetSeeder::class);
    }
}
