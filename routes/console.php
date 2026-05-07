<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send absence alerts every working day at 11 PM IST
Schedule::command('attendance:send-alerts')->dailyAt('23:00');

// Drain the email queue (runs every minute, exits when empty)
Schedule::command('queue:work --stop-when-empty --tries=3')->everyMinute()->withoutOverlapping();
