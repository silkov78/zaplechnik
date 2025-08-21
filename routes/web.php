<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\CheckIsPetyaSilkov;
use App\Models\Campground;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Campgrounds
Route::get('/campgrounds', function () {
    return view('campgrounds.index', ['campgrounds' => Campground::all()]);
})->middleware(CheckIsPetyaSilkov::class);
