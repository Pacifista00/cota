<?php

namespace App\Console;

use App\Jobs\ProcessMissingSensorData;
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
        // Execute scheduled feeds every minute
        $schedule->command('feed:execute-scheduled')
                 ->everyMinute()
                 ->name('execute-scheduled-feeds')
                 ->withoutOverlapping(2); // Prevent overlapping runs (2 minute timeout)

        // Cleanup pending feed executions (fallback safety net)
        $schedule->command('feed:cleanup-pending')
                 ->cron(config('feed.execution.cleanup_schedule', '*/5 * * * *'))
                 ->name('cleanup-pending-executions')
                 ->withoutOverlapping(5); // Prevent overlapping runs (5 minute timeout)

        // Sensor fallback mechanism - check for missing data every minute
        $schedule->job(new ProcessMissingSensorData())
                 ->everyMinute()
                 ->name('process-missing-sensor-data')
                 ->withoutOverlapping(2); // Prevent overlapping runs (2 minute timeout)
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
