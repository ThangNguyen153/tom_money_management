<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AccessController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'access'
], function () {
    Route::post('login', 'App\Http\Controllers\Auth\AccessController@login');
    Route::post('register', 'App\Http\Controllers\Auth\AccessController@register');

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'App\Http\Controllers\Auth\AccessController@logout');
        Route::get('my-profile', 'App\Http\Controllers\Auth\AccessController@getMyProfile');
    });
});

Route::group([
    'prefix' => 'manage',
    ], function(){
    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::delete('user', 'App\Http\Controllers\Auth\AccessController@deleteUser');
    });
});
