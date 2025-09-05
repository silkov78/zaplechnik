<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Scheduling

// TODO: Config scheduler for deleting expired tokens from db
//Schedule::command('sanctum:prune-expired --minutes=5')->everyFiveMinutes();
