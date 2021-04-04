<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\InviteKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class InviteKeyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InviteKey::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $possibleValues = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return [
            'value'   => substr(str_shuffle(str_repeat($possibleValues, 5)), 0, 4),
            'game_id' => Game::factory()->create()->getKey(),
        ];
    }
}
