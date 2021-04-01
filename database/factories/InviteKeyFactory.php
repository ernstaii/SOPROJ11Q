<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\InviteKey;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        return [
            'value'   => Str::random(4),
            'game_id' => Game::factory()->create()->getKey(),
        ];
    }
}
