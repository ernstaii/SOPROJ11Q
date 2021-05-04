<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\BorderMarker;
use App\Models\Game;
use App\Models\InviteKey;
use App\Models\Loot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ConstantGameSeeder extends Seeder
{
    const LOOT_NAMES = ['Station', 'Park', 'Kasteel', 'Skibaan', 'Museum', 'Albert Heijn', 'Jumbo', 'Coop', 'Spar', 'Hotel', 'Sportcentrum', 'Zwembad', 'Gym', 'Action', 'Blokker', 'Kruidvat', 'Coffeeshop', 'Shell', 'TinQ', 'HEMA', 'Praxis', 'Kwantum', 'Ikea', 'Golfbaan', 'Ziekenhuis', 'Dierenarts', 'Nettorama', 'BP', 'Universiteit', 'School', 'Restaurant', 'Snackbar'];

    public function run()
    {
        // ==================== Game 1 ====================
        $user_1 = User::create([
            'username' => 'Willem',
            'location' => $this->getRandLocationNearOss()
        ]);
        $user_2 = User::create([
            'username' => 'Bart',
            'location' => $this->getRandLocationNearOss()
        ]);

        $loot = new Collection();
        for ($i = 0; $i < rand(3, 10); $i++) {
            $loot->push(Loot::create([
                'name' => self::LOOT_NAMES[rand(0, count(self::LOOT_NAMES) - 1)],
                'location' => $this->getRandLocationNearOss()
            ]));
        }

        $game = Game::create([
            'police_station_location' => $this->getRandLocationNearOss()
        ]);

        foreach ($loot as $loot_item) {
            $game->loot()->attach($loot_item->id);
        }

        for ($i = 1; $i < 6; $i++) {
            BorderMarker::create([
                'location' => $this->getRandLocationForMarkersNearOss($i),
                'game_id' => $game->id
            ]);
        }

        // Used keys
        InviteKey::create([
            'value' => 'A0B3',
            'game_id' => $game->id,
            'user_id' => $user_1->id,
            'role' => Roles::Thief
        ]);
        InviteKey::create([
            'value' => 'LKJ3',
            'game_id' => $game->id,
            'user_id' => $user_2->id,
            'role' => Roles::Police
        ]);

        // Available keys
        InviteKey::create([
            'value' => 'YE3N',
            'game_id' => $game->id,
            'role' => Roles::Police
        ]);
        InviteKey::create([
            'value' => '78GD',
            'game_id' => $game->id,
            'role' => Roles::Police
        ]);
        InviteKey::create([
            'value' => 'P7BB',
            'game_id' => $game->id,
            'role' => Roles::Thief
        ]);
        InviteKey::create([
            'value' => 'PO03',
            'game_id' => $game->id,
            'role' => Roles::Thief,
        ]);
        InviteKey::create([
            'value' => 'AN4E',
            'game_id' => $game->id,
            'role' => Roles::Thief,
        ]);
        InviteKey::create([
            'value' => 'BNJE',
            'game_id' => $game->id,
            'role' => Roles::Thief
        ]);

        // ==================== Game 2 ====================
        $user_1 = User::create([
            'username' => 'Johan',
            'location' => $this->getRandLocationNearOss()
        ]);
        $user_2 = User::create([
            'username' => 'Rik',
            'location' => $this->getRandLocationNearOss()
        ]);

        $loot = new Collection();
        for ($i = 0; $i < rand(3, 10); $i++) {
            $loot->push(Loot::create([
                'name' => self::LOOT_NAMES[rand(0, count(self::LOOT_NAMES) - 1)],
                'location' => $this->getRandLocationNearOss()
            ]));
        }

        $game = Game::create([
            'police_station_location' => $this->getRandLocationNearOss()
        ]);

        foreach ($loot as $loot_item) {
            $game->loot()->attach($loot_item->id);
        }

        for ($i = 1; $i < 6; $i++) {
            BorderMarker::create([
                'location' => $this->getRandLocationForMarkersNearOss($i),
                'game_id' => $game->id
            ]);
        }

        // Used keys
        InviteKey::create([
            'value' => 'BF3V',
            'game_id' => $game->id,
            'user_id' => $user_1->id,
            'role' => Roles::Thief
        ]);
        InviteKey::create([
            'value' => 'FF3Q',
            'game_id' => $game->id,
            'user_id' => $user_2->id,
            'role' => Roles::Police
        ]);

        // Available keys
        InviteKey::create([
            'value' => 'HDI8',
            'game_id' => $game->id,
            'role' => Roles::Police
        ]);
        InviteKey::create([
            'value' => 'A3AF',
            'game_id' => $game->id,
            'role' => Roles::Thief
        ]);
        InviteKey::create([
            'value' => 'B3ND',
            'game_id' => $game->id,
            'role' => Roles::Thief
        ]);
    }

    private function getRandLocationNearOss()
    {
        $latitude = "51.7" . strval(rand(43866, 79043));
        $longitude = "5." . strval(rand(491387, 553818));
        return $latitude . ',' . $longitude;
    }

    private function getRandLocationForMarkersNearOss($number)
    {
        switch($number) {
            case 1:
                return "51.7" . strval(rand(30421, 34703)) . ",5." . strval(rand(470841, 482090));
            case 2:
                return "51.7" . strval(rand(31104, 35002)) . ",5." . strval(rand(593363, 598034));
            case 3:
                return "51.7" . strval(rand(90136, 93482)) . ",5." . strval(rand(576329, 579863));
            case 4:
                return "51.7" . strval(rand(76128, 79084)) . ",5." . strval(rand(528871, 543762));
            case 5:
                return "51.7" . strval(rand(80532, 82789)) . ",5." . strval(rand(474034, 497632));
            default:
                return "51.7" . strval(rand(30000, 33000)) . ",5." . strval(rand(470000, 480000));
        }
    }
}
