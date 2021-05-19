<?php

namespace Database\Factories;

use App\Enums\Statuses;
use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition()
    {
        return [
            'password' => Hash::make('password'),
            'status' => Statuses::Config,
            'duration' => 120,
            'interval' => 60,
            'time_left' => 7200,
            'police_station_location' => '51.763965, 5.529218',
            'thieves_score' => 0,
            'police_score' => 0,
            'colour_theme' => '#000000'
        ];
    }

    public function inConfig()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Statuses::Config,
            ];
        });
    }

    public function ongoing()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Statuses::Ongoing,
            ];
        });
    }

    public function paused()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Statuses::Paused,
            ];
        });
    }

    public function finished()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Statuses::Finished,
            ];
        });
    }
}
