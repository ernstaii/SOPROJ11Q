<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\Game;
use App\Models\InviteKey;
use Illuminate\Database\Seeder;

class InviteKeysTableSeeder extends Seeder
{
    public function run()
    {
        $id = Game::all()->first()->id;

        InviteKey::create([
            'value' => 'A0B3',
            'game_id' => $id,
            'role' => Roles::Thief
        ]);

        InviteKey::create([
            'value' => 'P7BB',
            'game_id' => $id,
            'role' => Roles::Thief
        ]);

        InviteKey::create([
            'value' => 'YE3N',
            'game_id' => $id,
            'role' => Roles::Police
        ]);

        InviteKey::create([
            'value' => 'PO03',
            'game_id' => $id,
            'role' => Roles::Thief
        ]);

        InviteKey::create([
            'value' => 'AN4E',
            'game_id' => $id,
            'role' => Roles::Police
        ]);

        InviteKey::create([
            'value' => 'LKJ3',
            'game_id' => $id,
            'role' => Roles::Police
        ]);

        // This one is not in use
        InviteKey::create([
            'value' => 'BNJE',
            'game_id' => $id,
            'role' => Roles::Thief,
        ]);

        // This one is not in use
        InviteKey::create([
            'value' => 'FF3Q',
            'game_id' => $id,
            'role' => Roles::Thief,
        ]);

        $id = Game::all()->last()->id;

        InviteKey::create([
            'value' => '156M',
            'game_id' => $id,
            'role' => Roles::Police
        ]);

        InviteKey::create([
            'value' => 'BF3V',
            'game_id' => $id,
            'role' => Roles::Police
        ]);

        // This one is not in use
        InviteKey::create([
            'value' => 'BNJE',
            'game_id' => $id,
            'role' => Roles::Thief
        ]);

        // This one is in use
        InviteKey::create([
            'value' => 'FF3Q',
            'game_id' => $id,
            'role' => Roles::Thief
        ]);
    }
}
