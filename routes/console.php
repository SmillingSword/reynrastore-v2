<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Diggie price updates every hour
Schedule::command('diggie:update-prices')->hourly();

// Schedule Diggie order status checks every 5 minutes
Schedule::command('diggie:check-status')->everyFiveMinutes();

// Schedule DigiFlazz product sync daily at 2 AM
Schedule::command('diggie:sync-products')->dailyAt('02:00');
