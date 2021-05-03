<?php

namespace Database\Factories;

use App\Enums\UserStatuses;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $latitude = "51.7" . strval(rand(43866, 79043));
        $longitude = "5." . strval(rand(491387, 553818));

        return [
            'username' => 'User ' . rand(0, 99999),
            'location' => $latitude . ',' . $longitude
        ];
    }

    public function inLobby()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => UserStatuses::InLobby,
            ];
        });
    }

    public function playing()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => UserStatuses::Playing,
            ];
        });
    }

    public function caught()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => UserStatuses::Caught,
            ];
        });
    }
}
