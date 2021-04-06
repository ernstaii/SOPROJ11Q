<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ConfigController;

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

Route::get('/', [ConfigController::class, 'index'])->name('index');
Route::post('/createGame', [ConfigController::class, 'createGame'])->name('GoToGame');
Route::get('/removeGame/{id}', [ConfigController::class, 'removeGame'])->name('RemoveGame');
Route::get('/game/{id}', [ConfigController::class, 'gameScreen'])->name('GameScreen');
Route::put('/game/{id}', [ConfigController::class, 'startGame']);
Route::post('/storeKeys', [ConfigController::class, 'storeKeys']);
