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

Route::get('/', 'App\Http\Controllers\ConfigController@index');
Route::get('/game/{id}', 'App\Http\Controllers\ConfigController@gameScreen');
Route::post('/storeKeys', 'App\Http\Controllers\ConfigController@storeKeys');
