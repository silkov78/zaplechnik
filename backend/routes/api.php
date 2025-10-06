<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampgroundsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login','login')->middleware(['throttle:auth']);
        Route::post('/register','register')->middleware(['throttle:auth']);
        Route::post('/logout','logout')->middleware('auth:sanctum');
    });

    // Profile
    Route::controller(ProfileController::class)
        ->middleware('auth:sanctum')
        ->prefix('me')
        ->group(function () {
            Route::get('/', 'show');
            Route::patch('/', 'update');
            Route::delete('/', 'destroy');
    });

    // Campgrounds
    Route::controller(CampgroundsController::class)->group(function () {
        Route::get('/campgrounds', 'index');
        Route::get('/campgrounds/visited', 'indexVisited')
            ->middleware('auth:sanctum');
    });

    // Visits
    Route::controller(VisitsController::class)
        ->middleware('auth:sanctum')
        ->group(function () {
            Route::post('/visits', 'store');
            Route::delete('/visits', 'destroy');
    });
});
