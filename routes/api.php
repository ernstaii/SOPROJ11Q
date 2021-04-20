<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\InviteKeyController;
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

Route::apiResource('users', UserController::class)->only(['store', 'update']);
Route::get('/users/{user}', [UserController::class, 'get']);

Route::group(['middleware' => ['api']], function (Router $router) {
    $router->get('/invite-keys/{value}', [InviteKeyController::class, 'get']);
    $router->get('/games/{game}', [GameController::class, 'get']);
    $router->get('/games/{game}/users', [GameController::class, 'getUsers']);
    $router->get('/games/{game}/loot', [GameController::class, 'getLoot']);
});
