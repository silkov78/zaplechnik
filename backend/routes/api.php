<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampgroundsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth
    Route::controller(AuthController::class)
        ->middleware(['throttle:strict'])
        ->group(function () {
        Route::post('/login','login');
        Route::post('/register','register');
        Route::post('/logout','logout')->middleware(['auth:sanctum']);
    });

    // Profile
    Route::controller(ProfileController::class)
        ->middleware('auth:sanctum')
        ->prefix('me')
        ->group(function () {
            Route::get('/', 'show')->middleware(['throttle:medium']);
            Route::patch('/', 'update')->middleware(['throttle:medium']);
            Route::delete('/', 'destroy')->middleware(['throttle:strict']);
    });

    // Campgrounds
    Route::controller(CampgroundsController::class)->group(function () {
        Route::get('/campgrounds', 'index')->middleware(['throttle:lite']);
        Route::get('/campgrounds/visited', 'indexVisited')
            ->middleware('auth:sanctum');
    });

    // Visits
    Route::controller(VisitsController::class)
        ->middleware(['throttle:medium', 'auth:sanctum'])
        ->group(function () {
            Route::post('/visits', 'store');
            Route::delete('/visits', 'destroy');
    });
});
