<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\MessageController;

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

Route::post('/sanctum/token', TokenController::class);

Route::middleware(['auth:sanctum', 'apply_locale'])->group(function () {

    /**
     * Auth related
     */
    Route::get('/users/auth', AuthController::class);

    /**
     * Users
     */
    Route::resource('users', UserController::class);

    /**
     * Messages
     */
    Route::resource('messages', MessageController::class);

});
