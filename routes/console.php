<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Task Scheduling: Cancel Expired Bookings
|--------------------------------------------------------------------------
|
| Jalankan setiap menit untuk membatalkan booking 'pending' yang sudah
| melewati batas waktu pembayaran (15 menit). Ini memastikan kamar
| yang ditahan tapi tidak dibayar akan kembali tersedia.
|
| Untuk mengaktifkan di server production, tambahkan ke crontab:
| * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
|
*/
Schedule::command('app:cancel-expired-bookings')->everyMinute();
