<?php

namespace Database\Factories;

use App\Enums\Roles;
use App\Models\InviteKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class InviteKeyFactory extends Factory
{
    const ALPHANUMERIC_CAPITALS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    protected $model = InviteKey::class;

    public function definition()
    {
        return [
            'value' => $this->createKeyString(),
            'role' => Roles::Police
        ];
    }

    private function createKeyString()
    {
        $key = "";
        for ($j = 0; $j < 4; $j++) {
            $key .= self::ALPHANUMERIC_CAPITALS[rand(0, (count(self::ALPHANUMERIC_CAPITALS) - 1))];
        }
        return $key;
    }
}
