<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('users/{user}', [UserController::class, 'get'])->name('users.get');
Route::apiResource('users', UserController::class)->only(['store', 'update']);

Route::group(['middleware' => ['api']], function (Router $router) {
    $router->get('/invite-key/{inviteKeyId}', [UserController::class, 'getInviteKey']);
    $router->get('/game/{gameId}/users', [GameController::class, 'getUsersInGame']);
    $router->get('/game/{gameId}/loot', [GameController::class, 'getLootInGame']);
    $router->get('/game/{gameId}/status', [GameController::class, 'getStatusInGame']);
});
