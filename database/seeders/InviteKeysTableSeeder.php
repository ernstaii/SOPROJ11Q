<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\InviteKey;
use Illuminate\Database\Seeder;

class InviteKeysTableSeeder extends Seeder
{
    public function run()
    {
        InviteKey::create([
            'value' => 'A0B3',
            'game_id' => 1,
            'role' => Roles::Thief
        ]);

        InviteKey::create([
            'value' => 'P7BB',
            'game_id' => 1,
            'role' => Roles::Thief
        ]);

        InviteKey::create([
            'value' => 'YE3N',
            'game_id' => 1,
            'role' => Roles::Police
        ]);

        InviteKey::create([
            'value' => 'PO03',
            'game_id' => 1,
            'role' => Roles::Thief
        ]);

        InviteKey::create([
            'value' => 'AN4E',
            'game_id' => 1,
            'role' => Roles::Police
        ]);

        InviteKey::create([
            'value' => 'LKJ3',
            'game_id' => 1,
            'role' => Roles::Police
        ]);

        InviteKey::create([
            'value' => 'BNJE',
            'game_id' => 2,
            'role' => Roles::Thief
        ]);

        InviteKey::create([
            'value' => '156M',
            'game_id' => 2,
            'role' => Roles::Police
        ]);

        InviteKey::create([
            'value' => 'BF3V',
            'game_id' => 2,
            'role' => Roles::Police
        ]);

        InviteKey::create([
            'value' => 'FF3Q',
            'game_id' => 2,
            'role' => Roles::Thief
        ]);
    }
}
