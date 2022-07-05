<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('fbads:update_campaign_insights')->everyMinute();
        $schedule->command('orders:update')->everyMinute();
        $schedule->command('products:update')->everyTwoHours();
        $schedule->command('fbads:update_campaigns')->everyFourMinutes();
        $schedule->command('fbads:update_adsets')->everyFiveMinutes();
        $schedule->command('fbads:update_ad_creative')->everyTwoMinutes();
        $schedule->command('fbads:update_ads_insights')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
