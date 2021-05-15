<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\InviteKeyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return redirect('/games');
})->name('index');

Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::post('/games', [GameController::class, 'store'])->name('games.store');
Route::post('/presets', [GameController::class, 'storeGamePreset'])->name('presets.store');
Route::get('/games/{game}', [GameController::class, 'show'])->name('games.show');
Route::put('/games/{game}', [GameController::class, 'update'])->name('games.update');
Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('games.destroy');
Route::post('/games/{game}/invite-keys', [InviteKeyController::class, 'generateKeys'])->name('games.invite-keys.store');
Route::post('/games/{game}/border-markers', [GameController::class, 'storeMarkers'])->name('games.border-markers.store');
Route::post('/games/{game}/loot', [GameController::class, 'storeLoot'])->name('games.loot.store');
Route::patch('/games/{game}/police-station', [GameController::class, 'setPoliceStationLocation'])->name('games.police-station.update');
Route::post('/games/{game}/notifcations', [GameController::class, 'sendNotification'])->name('games.sendMessage');
