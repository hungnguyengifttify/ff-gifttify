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
        $schedule->command('fbads:update_campaigns')->cron('*/9 * * * *');
        $schedule->command('fbads:update_adsets')->everyTenMinutes();
        $schedule->command('fbads:update_ad_creative')->everyTwoMinutes();
        $schedule->command('fbads:update_ads_insights')->everyMinute();
        $schedule->command('fbads:update_ads_insights all')->everyTwoHours();
        $schedule->command('fbads:update_ads')->everyFiveMinutes();
        $schedule->command('googledrive:get_files 100')->everyFifteenMinutes();
        $schedule->command('googledrive:trashed_files')->everyTwoHours();
        $schedule->command('fbacc:update')->everyMinute();
        $schedule->command('ga:crawl_google_analytic_campaigns')->everyMinute();
        $schedule->command('products_csv:import store.gifttify.com')->everyMinute();
        $schedule->command('products_csv:import thecreattify.co')->everyMinute();
        $schedule->command('products_csv:import 66circle.com')->everyMinute();
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
