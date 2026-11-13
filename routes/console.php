<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('content:publish-scheduled')->hourly();
Schedule::command('sitemap:generate')->hourly();
Schedule::command('indexnow:submit')->daily();
Schedule::command('affiliate-links:check')->weekly();
