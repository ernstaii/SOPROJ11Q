<?php

use App\Http\Controllers\AppErrorController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\InviteKeyController;
use App\Http\Controllers\LootController;
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

Route::group(['middleware' => ['api']], function (Router $router) {
    $router->get('/users/{user}', [UserController::class, 'get']);
    $router->get('/invite-keys/{key}', [InviteKeyController::class, 'get']);
    $router->get('/games/{game}', [GameController::class, 'get']);
    $router->get('/games/{game}/users', [GameController::class, 'getUsers']);
    $router->get('/games/{game}/users-with-role', [GameController::class, 'getUsersWithRole']);
    $router->get('/games/{game}/users-with-role-unfiltered', [GameController::class, 'getUsersWithRoleUnfiltered']);
    $router->get('/games/{game}/loot', [GameController::class, 'getLoot']);
    $router->get('/games/{game}/invite-keys', [GameController::class, 'getInviteKeys']);
    $router->get('/games/{game}/border-markers', [GameController::class, 'getBorderMarkers']);
    $router->get('/games/{game}/notifications', [GameController::class, 'getNotifications']);
    $router->get('/games/{game}/logo', [GameController::class, 'getLogo']);
    $router->get('/images/money-bag.png', [GameController::class, 'getLootIcon']);
    $router->get('/images/police-badge.png', [GameController::class, 'getPoliceStationIcon']);
    $router->get('/games/{game}/gadgets', [GameController::class, 'getGameGadgets']);
    $router->post('/games/{game}/notifications', [GameController::class, 'postNotification']);
    $router->post('/users/{user}/trigger-alarm', [UserController::class, 'triggerAlarm']);
    $router->post('/app-errors', [AppErrorController::class, 'store']);
    $router->patch('/games/{game}/thieves-score/{score}', [GameController::class, 'updateThievesScore']);
    $router->patch('/games/{game}/police-score/{score}', [GameController::class, 'updatePoliceScore']);
    $router->patch('/users/{user}/catch', [UserController::class, 'catchThief']);
    $router->patch('/users/{user}/gadgets/{gadget}', [UserController::class, 'updateGadget']);
    $router->delete('/loot/{loot}', [LootController::class, 'destroy']);
});
