<?php

namespace Database\Seeders;

use App\Models\Gadget;
use Illuminate\Database\Seeder;

class GadgetSeeder extends Seeder
{
    const NAMES = array('Rookgordijn', 'Alarm', 'Drone');

    public function run()
    {
        foreach (self::NAMES as $name)
            Gadget::create([
                'name' => $name
            ]);
    }
}
