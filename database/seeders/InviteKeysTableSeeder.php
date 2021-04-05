<?php

namespace Database\Seeders;

use App\Models\InviteKey;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InviteKeysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InviteKey::create([
            'value' => 'A0B3',
            'game_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        InviteKey::create([
            'value' => 'P7BB',
            'game_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        InviteKey::create([
            'value' => 'YE3N',
            'game_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        InviteKey::create([
            'value' => 'PO03',
            'game_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        InviteKey::create([
            'value' => 'AN4E',
            'game_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        InviteKey::create([
            'value' => 'LKJ3',
            'game_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        InviteKey::create([
            'value' => 'BNJE',
            'game_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        InviteKey::create([
            'value' => '156M',
            'game_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        InviteKey::create([
            'value' => 'BF3V',
            'game_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        InviteKey::create([
            'value' => 'FF3Q',
            'game_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
