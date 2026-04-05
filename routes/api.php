<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RefreshTokenController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->as('auth.')->group(function () {
    Route::post('/register', RegisterController::class)->middleware('throttle:5,1')->name('register');
    Route::post('/login', LoginController::class)->middleware('throttle:5,1')->name('login');
    Route::post('/logout', LogoutController::class)->middleware('auth:sanctum')->name('logout');
    Route::post('/refresh', RefreshTokenController::class)->middleware('auth:sanctum')->name('refresh');
});

Route::middleware(['auth:sanctum', 'ability:api:access'])->group(function () {
    Route::get('/user', [UserController::class, 'show'])->name('user.show');
});
