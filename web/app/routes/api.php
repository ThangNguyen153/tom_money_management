<?php

use Illuminate\Http\Request;
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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

/* GENERAL ROUTES */

Route::group([
    'prefix' => 'access'
], function () {
    Route::post('login', 'App\Http\Controllers\Auth\AccessController@login')->name('login');
    Route::post('register', 'App\Http\Controllers\Auth\AccessController@register')->name('register');

    // Verify email
    Route::get('/email/verify/{id}/{hash}', 'App\Http\Controllers\Auth\VerifyEmailController@__invoke')
        ->middleware(['cors', 'json.response', 'signed', 'throttle:6,1'])
        ->name('verification.verify')
    ;

    // Send reset-password link to given email
    Route::post('forgot-password', 'App\Http\Controllers\Auth\VerifyEmailController@resendResetPasswordEmail')
        ->middleware(['cors', 'json.response', 'guest', 'throttle:6,1'])
        ->name('password.email')
    ;
    // Show reset-password form when user clicks on "verify" button in email
    Route::get('/reset-password/{token}', function (Request $request,$token = null) {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    })->middleware('guest')->name('password.reset');

    // Reset password
    Route::post('/reset-password', 'App\Http\Controllers\Auth\VerifyEmailController@resetPassword')
        ->middleware('guest')
        ->name('password.update');


    // some APIs need middleware "auth:api"
    Route::group([
        'middleware' => ['cors', 'json.response', 'auth:api']
    ], function() {
        Route::get('logout', 'App\Http\Controllers\Auth\AccessController@logout')->name('logout');

        // Resend link to verify email
        Route::post('/email/verify/resend', 'App\Http\Controllers\Auth\VerifyEmailController@resendVerificationEmail')
            ->middleware(['throttle:6,1'])->name('verification.send');

        Route::post('change-password', 'App\Http\Controllers\Auth\AccessController@changePassword')->name('change-password');
    });

});

/* END GENERAL ROUTES */

/* ROUTES FOR ADMIN */

Route::group([
    'prefix' => 'manage',
    'middleware' => ['role:super-administrator|administrator']
    ], function(){
        Route::group([
            'middleware' => ['cors', 'json.response', 'auth:api']
        ], function() {
            Route::delete('user', 'App\Http\Controllers\Admin\API\UserController@deleteUser')->name('delete-user');
            Route::get('my-profile', 'App\Http\Controllers\Admin\API\UserController@getMyProfile')->name('get-admin-profile');
            Route::get('usage-types', 'App\Http\Controllers\Admin\API\UsageTypeController@getUsageTypes')->name('get-usage-types');
        });
});

/* END ROUTES FOR ADMIN */

/* ROUTES FOR USER */

Route::group([
    'prefix' => 'user',
    'middleware' => ['role:user']
], function(){
    Route::group([
        'middleware' => ['cors', 'json.response', 'auth:api']
    ], function() {
        Route::get('my-profile', 'App\Http\Controllers\User\API\UserController@getMyProfile')->name('get-user-profile');

        /* Payment method */
        Route::put('update-payment-methods','App\Http\Controllers\User\API\PaymentmethodController@updateUserPaymentMethods')->name('update-user-payment-methods');
        Route::put('update-balance','App\Http\Controllers\User\API\PaymentmethodController@updateUserBalance')->name('update-user-balance');

        /* Daily usage */
        Route::post('add-daily-usage','App\Http\Controllers\User\API\DailyUsageController@addDailyUsage')->name('add-user-daily-usage');
        Route::put('update-daily-usage','App\Http\Controllers\User\API\DailyUsageController@updateDailyUsage')->name('update-user-daily-usage');
        Route::delete('remove-daily-usage','App\Http\Controllers\User\API\DailyUsageController@removeDailyUsage')->name('remove-user-daily-usage');
    });
});

/* END ROUTES FOR USER */
