<?php

namespace App\Http\Controllers;

use App\Models\Loot;

class LootController extends Controller
{
    public function destroy(Loot $loot){
        $loot->delete();
    }
}
