<?php

use App\Http\Controllers\ProfileController;
use App\Models\Campground;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
