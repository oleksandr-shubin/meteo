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
         $schedule->command('subscription:process')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        $this->load(__DIR__ . '/../Domain/Weatherapi/Commands');
        $this->load(__DIR__ . '/../Domain/Subscription/Commands');
        $this->load(__DIR__ . '/../Domain/Visualcrossing/Commands');

        require base_path('routes/console.php');
    }
}
