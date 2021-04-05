<?php

namespace Database\Factories;

use App\Models\Loot;
use Illuminate\Database\Eloquent\Factories\Factory;

class LootFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Loot::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $latitude = "51.7" . strval(rand(43866, 79043));
        $longitude = "5." . strval(rand(491387, 553818));

        return [
            'game_id' => rand(1, 2),
            'name' => $this->faker->company(),
            'location' => $latitude . "," . $longitude
        ];
    }
}
