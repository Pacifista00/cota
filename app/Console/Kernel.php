<?php

namespace App\Console;

use App\Models\FeedSchedule;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedules = FeedSchedule::all();

        foreach ($schedules as $feedSchedule) {
            $time = Carbon::createFromFormat('H:i:s', $feedSchedule->waktu_pakan);

            // jalankan command pada jam & menit sesuai jadwal
            $schedule->command('feed:give')->cron("{$time->minute} {$time->hour} * * *");
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
