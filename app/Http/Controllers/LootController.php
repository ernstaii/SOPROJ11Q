<?php

namespace App\Http\Controllers;

use App\Models\Loot;
use Illuminate\Http\Request;

class LootController extends Controller
{
    public function destroy(Loot $loot){
        $loot->delete();
    }
}
