<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function (){
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


//\Illuminate\Support\Facades\Schedule::command('app:test-command')->everyMinute();


