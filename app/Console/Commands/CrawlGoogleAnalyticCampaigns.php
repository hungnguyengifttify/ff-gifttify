<?php

namespace App\Console\Commands;

use App\Services\GoogleAnalytics;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\FbAds;

class CrawlGoogleAnalyticCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     * []
     * @var string
     */
    protected $signature = 'ga:crawl_google_analytic_campaigns {from_time?} {to_time?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl Google Analytic Campaigns';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job running at ". now());

        $fromTime = $this->argument('from_time') ?? 'today';
        $toTime = $this->argument('to_time') ?? 'today';

        $gaModel = new GoogleAnalytics();
        $gaModel->crawlCampaigns($fromTime, $toTime);
        //to do
//        dd($gaModel->crawlCampaigns($fromTime, $toTime));
        $this->info("Cron Job end at ". now());
        $this->info('Success!');
    }


}
