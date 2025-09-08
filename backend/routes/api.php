<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login','login')->name('login');
        Route::post('/register','register')->name('register');
        Route::post('/logout','logout')->name('logout')->middleware('auth:sanctum');
    });

    // Profile
    Route::controller(ProfileController::class)->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/me', 'show')->name('me.show');
            Route::patch('/me', 'update')->name('me.update');
//            Route::delete('/me', 'destroy')->name('me.destroy');
        });
    });
});
