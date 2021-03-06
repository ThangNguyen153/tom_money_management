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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', 'App\Http\Controllers\Auth\Web\AccessController@showLoginForm')->name('login-form');
Route::post('/login', 'App\Http\Controllers\Auth\Web\AccessController@login')->name('login');
Route::get('/logout', 'App\Http\Controllers\Auth\Web\AccessController@logout')->name('logout');

Route::group([
        'prefix' => 'user',
        'middleware' => ['web_check_login']
    ], function(){
        Route::get('/daily-usage', 'App\Http\Controllers\Auth\Web\AccessController@getDailyUsage')->name('user-daily-usage');
        Route::get('/statistics', 'App\Http\Controllers\Auth\Web\AccessController@getUsageStatistics')->name('user-usage-statistics');
        Route::get('/activities', 'App\Http\Controllers\Auth\Web\AccessController@getActivityLog')->name('user-activity-log');
});
