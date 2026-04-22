<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Notification;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Delete notifications older than 7 days
Schedule::command('model:prune', [
    '--model' => [Notification::class],
])->daily();

// Sync missed order statuses from Waseet API periodically
Schedule::command('waseet:sync-statuses')->everyThirtyMinutes();
