<?php

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

Route::get('/', 'App\Http\Controllers\ConfigController@index')->name('index');
Route::post('/createGame', 'App\Http\Controllers\ConfigController@createGame')->name('GoToGame');
Route::get('/removeGame/{id}', 'App\Http\Controllers\ConfigController@removeGame')->name('RemoveGame');
Route::get('/game/{id}', 'App\Http\Controllers\ConfigController@gameScreen')->name('GameScreen');
Route::post('/storeKeys', 'App\Http\Controllers\ConfigController@storeKeys');
