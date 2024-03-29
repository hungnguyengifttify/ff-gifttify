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
        $schedule->command('fbads:update_campaigns')->everyFiveMinutes(); #cron('*/9 * * * *');
        $schedule->command('fbads:update_adsets')->everyTenMinutes();
        $schedule->command('fbads:update_ad_creative')->everyTwoMinutes();
        $schedule->command('fbads:update_ads_insights')->everyMinute();
        $schedule->command('fbads:update_ads_insights all')->everyTwoHours();
        $schedule->command('fbads:update_ads')->everyTenMinutes();
        $schedule->command('googledrive:get_files 100')->everyFifteenMinutes();
        $schedule->command('googledrive:trashed_files')->everyTwoHours();
        $schedule->command('fbacc:update')->everyFiveMinutes();
        $schedule->command('ga:crawl_google_analytic_campaigns')->everyMinute();
        $schedule->command('products_csv:import store.gifttify.com')->everyMinute();
        $schedule->command('products_csv:import thecreattify.co')->everyMinute();
        $schedule->command('products_csv:import 66circle.com')->everyMinute();
        $schedule->command('products_csv:import owllify.com')->everyMinute();
        $schedule->command('products_csv:import vanoba.com')->everyMinute();
        $schedule->command('products_csv:import whelands.com')->everyMinute();
        $schedule->command('mailchimp:push_order')->everyFiveMinutes();
        $schedule->command('remix:scheduleproduct')->everyMinute();
        $schedule->command('remix:reviewproduct')->cron('0 1 * * *');
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
