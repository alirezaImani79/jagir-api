<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccommodationController;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('users')
        ->name('users.')
        ->middleware([
            EnsureUserHasRole::class . ':admin'
        ])
        ->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

    Route::prefix('accommodations')
        ->name('accommodations.')
        ->middleware([
            EnsureUserHasRole::class . ':admin'
        ])
        ->group(function() {
            Route::get('/', [AccommodationController::class, 'index'])->name('index');
            Route::get('/{accommodation}', [AccommodationController::class, 'show'])->name('show');
            Route::post('/', [AccommodationController::class, 'create'])->name('create');
            Route::put('/{accommodation}', [AccommodationController::class, 'update'])->name('update');
            Route::delete('/{accommodation}', [AccommodationController::class, 'destroy'])->name('destroy');
        });
});

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('otp/verify', [AuthController::class, 'verifyOTPCode']);
    Route::post('otp/send', [AuthController::class, 'sentOtpCode']);
});
