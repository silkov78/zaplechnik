<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampgroundsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login','login')->name('auth.login');
        Route::post('/register','register')->name('auth.register');
        Route::post('/logout','logout')
            ->name('auth.logout')
            ->middleware('auth:sanctum');
    });

    // Profile
    Route::controller(ProfileController::class)->prefix('me')->name('me')->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            // Profile data
            Route::get('/', 'show')->name('show');
            Route::patch('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');

            // Visited campgrounds
            Route::get('/visited-campgrounds', 'visitedCampgrounds')->name('visitedCampgrounds');

            // Visits
            Route::post('/visits', 'storeVisit')->name('visits.store');
            Route::delete('/visits/{visit}', 'destroyVisit')->name('visits.destroy');
        });
    });

    // Campgrounds
    Route::controller(CampgroundsController::class)->group(function () {
        Route::get('/campgrounds', 'index')->name('campgrounds.index');
    });
});
