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
        $inviteKey = InviteKey::factory()->create();

        return [
            'username' => $this->faker->userName,
            'location' => '51.498134,-0.201755',
            'invite_key' => $inviteKey->getAttribute('value'),
            'game_id' => $inviteKey->getAttribute('game_id'),
            'role' => Roles::Thief,
        ];
    }
}
