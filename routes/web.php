<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\CheckIsPetyaSilkov;
use App\Models\Campground;
use App\Models\User;
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

// Administration
Route::prefix('/administration')->middleware(CheckIsPetyaSilkov::class)->group(function () {
    Route::get('/map', function () {
        return view('campgrounds.index', ['campgrounds' => Campground::all()]);
    });

    Route::get('/users', function () {
        return view('users.index', ['users' => User::all()]);
    });
});
