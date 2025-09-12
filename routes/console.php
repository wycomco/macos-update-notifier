<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the daily macOS update check
Schedule::command('macos:check-updates')
    ->daily()
    ->at('09:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->emailOutputOnFailure(config('mail.from.address'));
