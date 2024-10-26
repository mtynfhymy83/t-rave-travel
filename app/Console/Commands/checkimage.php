<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

class checkimage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:checkimage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(Schedule $schedule)
    {
        $schedule->call(function () {
            // لیست فایل‌ها از دایرکتوری public/local
            $files = Storage::files('public/local');

            foreach ($files as $file) {
                // بررسی زمان آخرین تغییر فایل
                $lastModified = Storage::lastModified($file);
                // محاسبه زمان انقضا (15 دقیقه قبل)
                if (Carbon::createFromTimestamp($lastModified)->addMinutes(15)->isPast()) {
                    // اگر فایل منقضی شده است، آن را حذف کنید
                    Storage::delete($file);
                }
            }
        })->everyMinute(); // تنظیم برای اجرا هر دقیقه

    }

}
