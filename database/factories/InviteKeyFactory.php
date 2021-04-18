<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\InviteKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class InviteKeyFactory extends Factory
{
    protected $model = InviteKey::class;

    public function definition()
    {
        $possibleValues = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return [
            'value' => substr(str_shuffle(str_repeat($possibleValues, 5)), 0, 4),
            'game_id' => Game::create()->id,
        ];
    }
}
