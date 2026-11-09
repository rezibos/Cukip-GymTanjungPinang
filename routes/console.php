<?php

use App\Models\Membership;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(fn (): int => Membership::synchronizeDateStatuses())
    ->name('memberships:synchronize-date-statuses')
    ->dailyAt('00:05');
