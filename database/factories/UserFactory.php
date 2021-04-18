<?php

namespace Database\Factories;

use App\Enums\Roles;
use App\Models\InviteKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'username' => $this->faker->userName,
            'location' => '51.498134,-0.201755',
            'invite_key' => InviteKey::factory()->create()->getAttribute('value'),
            'role' => Roles::Thief,
        ];
    }
}
