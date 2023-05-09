<?php

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

//Register user
Route::post('auth/register', \App\Http\Controllers\Api\V1\Auth\RegisterController::class);
//Login user
Route::post('auth/login', \App\Http\Controllers\Api\V1\Auth\LoginController::class);
//List of available zones
Route::get('zones', [\App\Http\Controllers\Api\V1\Zone\ZoneController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    //Show user profile data
    Route::get('/profile', [\App\Http\Controllers\Api\V1\Profile\ProfileController::class, 'show']);
    //Change user email
    Route::put('/profile', [\App\Http\Controllers\Api\V1\Profile\ProfileController::class, 'update']);
    //Change user password
    Route::put('/password', \App\Http\Controllers\Api\V1\Password\PasswordUpdateController::class);
    //Vehicle managing
    Route::apiResource('vehicles', \App\Http\Controllers\Api\V1\Vehicle\VehicleController::class);
    //SHow single parking data
    Route::get('parkings/{parking}', [\App\Http\Controllers\Api\V1\Parking\ParkingController::class, 'show']);
    //Start parking
    Route::post('parkings/start', [\App\Http\Controllers\Api\V1\Parking\ParkingController::class, 'start']);
    //Stop parking
    Route::put('parkings/{parking}', [\App\Http\Controllers\Api\V1\Parking\ParkingController::class, 'stop']);

    //Logout
    Route::post('/logout', \App\Http\Controllers\Api\V1\Auth\LogoutController::class);
});
