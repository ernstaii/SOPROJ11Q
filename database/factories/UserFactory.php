<?php

namespace Database\Factories;

use App\Enums\Roles;
use App\Models\InviteKey;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $inviteKey = InviteKey::factory()->create();

        return [
            'username' => 'Random name',
            'location' => '51.498134,-0.201755',
            'invite_key' => $inviteKey->getAttribute('value'),
            'game_id' => intval($inviteKey->getAttribute('game_id')),
            'role' => Roles::Thief,
        ];
    }
}
