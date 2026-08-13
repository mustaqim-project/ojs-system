<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SendReviewReminders;
use App\Console\Commands\SendReviewEscalations;
use App\Console\Commands\PublishScheduledIssues;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Send review reminders every day at 8 AM
        $schedule->command(SendReviewReminders::class)->dailyAt('08:00');

        // Send review escalations every day at 9 AM
        $schedule->command(SendReviewEscalations::class)->dailyAt('09:00');

        // Publish scheduled issues every hour
        $schedule->command(PublishScheduledIssues::class)->hourly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        if (file_exists(base_path('routes/console.php'))) {
            require base_path('routes/console.php');
        }
    }
}
