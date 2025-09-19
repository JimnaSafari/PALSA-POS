<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Send daily sales report every day at 8 AM
        $schedule->command('pos:daily-sales-report')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->runInBackground();

        // Check for low stock alerts every 6 hours
        $schedule->command('pos:low-stock-alerts')
            ->everySixHours()
            ->withoutOverlapping()
            ->runInBackground();

        // Clean up old data weekly on Sundays at 2 AM
        $schedule->command('pos:cleanup-old-data')
            ->weeklyOn(0, '02:00')
            ->withoutOverlapping()
            ->runInBackground();

        // Clear expired cache entries daily at 3 AM
        $schedule->command('cache:prune-stale-tags')
            ->dailyAt('03:00');

        // Backup database daily at 1 AM (if backup package is installed)
        // $schedule->command('backup:run --only-db')
        //     ->dailyAt('01:00')
        //     ->withoutOverlapping();

        // Queue maintenance
        $schedule->command('queue:prune-batches --hours=48')
            ->dailyAt('04:00');

        // Log rotation (if needed)
        $schedule->command('log:clear')
            ->monthlyOn(1, '05:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}