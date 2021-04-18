<?php

namespace Database\Factories;

use App\Models\Loot;
use Illuminate\Database\Eloquent\Factories\Factory;

class LootFactory extends Factory
{
    protected $model = Loot::class;

    public function definition()
    {
        $latitude = "51.7" . strval(rand(43866, 79043));
        $longitude = "5." . strval(rand(491387, 553818));

        return [
            'name' => $this->faker->company(),
            'location' => $latitude . "," . $longitude
        ];
    }
}
