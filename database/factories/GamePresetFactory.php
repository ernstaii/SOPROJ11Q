<?php

namespace Database\Factories;

use App\Models\GamePreset;
use Illuminate\Database\Eloquent\Factories\Factory;

class GamePresetFactory extends Factory
{
    protected $model = GamePreset::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'duration' => 120,
            'interval' => 60,
            'police_station_location' => '51.763965,5.529218',
        ];
    }
}
