<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->name('users.')->group(function() {
    Route::get('/{user}', [UserController::class, 'show']);
});

Route::prefix('auth')->name('auth.')->group(function() {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('otp/verify', [AuthController::class, 'verifyOTPCode']);
});
