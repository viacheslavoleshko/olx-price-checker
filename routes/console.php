<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command('queue:work --timeout=900 --memory=512 --tries=5 --stop-when-empty')->runInBackground()->everyMinute();
Schedule::command('app:process-check-url-price')->everyMinute();