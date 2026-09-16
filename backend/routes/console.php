<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\DeteksiPengingatKgbJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// KGB reminder detection — runs daily at 07:00
Schedule::job(new DeteksiPengingatKgbJob)->dailyAt('07:00');
