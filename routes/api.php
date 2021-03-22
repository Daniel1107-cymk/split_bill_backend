<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

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

Route::prefix('v1')->group(function() {
    // auth api
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function() {
        Route::get('me', [UserController::class, 'me']);
        Route::post('update-profile', [UserController::class, 'updateProfile']);
        Route::post('change-password', [UserController::class, 'changePassword']);
        Route::get('bill', [UserController::class, 'indexBill']);
        Route::post('bill', [UserController::class, 'storeBill']);
    });
});

