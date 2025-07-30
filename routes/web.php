<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/map', function () {
    return \App\Models\Camping::all()->toArray();
});
