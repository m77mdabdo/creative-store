<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// The cart reminders run from bootstrap/app.php with the other store jobs.
Schedule::command('shipments:sync')->everyThirtyMinutes()->withoutOverlapping();
