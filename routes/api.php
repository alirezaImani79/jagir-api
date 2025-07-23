<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\ConfigController;

Route::prefix('config')
    ->name('config.')
    ->group(function() {
        Route::get('/provinces', [ConfigController::class, 'provinces'])->name('provinces');
        Route::get('/{province}/cities', [ConfigController::class, 'cities'])->name('province.cities');
    });

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('users')
        ->name('users.')
        ->middleware([
            'roles:admin'
        ])
        ->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
});

Route::prefix('accommodations')
        ->name('accommodations.')
        ->group(function() {
            Route::get('/', [AccommodationController::class, 'index'])->name('index');
            Route::get('/{accommodation}', [AccommodationController::class, 'show'])->name('show');
            Route::post('/', [AccommodationController::class, 'create'])
                ->middleware([
                    'roles:admin,service_provider'
                ])
                ->name('create');
            Route::put('/{accommodation}', [AccommodationController::class, 'update'])->name('update');
            Route::delete('/{accommodation}', [AccommodationController::class, 'destroy'])->name('destroy');
        });

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('otp/verify', [AuthController::class, 'verifyOTPCode']);
    Route::post('otp/send', [AuthController::class, 'sentOtpCode']);
});
