<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampgroundsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login','login');
        Route::post('/register','register');
        Route::post('/logout','logout')->middleware('auth:sanctum');
    });

    // Profile
    Route::controller(ProfileController::class)
        ->middleware('auth:sanctum')
        ->prefix('me')
        ->group(function () {
            // Profile data
            Route::get('/', 'show');
            Route::patch('/', 'update');
            Route::delete('/', 'destroy');

            // Profile visits
            Route::post('/visits', 'storeVisit');
            Route::delete('/visits', 'destroyVisit');
    });

    // Campgrounds
    Route::controller(CampgroundsController::class)->group(function () {
        // All campgrounds
        Route::get('/campgrounds', 'index');

        // Visited campgrounds
        Route::get('/me/visited-campgrounds', 'visitedCampgrounds')
            ->middleware('auth:sanctum');
    });
});
