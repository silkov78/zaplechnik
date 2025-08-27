<?php

use App\Models\Campground;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/map', function () {
    return response(Campground::all()->toJson());
});