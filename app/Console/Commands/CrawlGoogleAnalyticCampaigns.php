<?php

namespace App\Console\Commands;

use App\Services\GoogleAnalytics;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\FbAds;
use App\Models\GaCampaignReports;

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

        $date_record = '';
        if($fromTime == 'today' && $toTime == 'today'){
            $date_record = Carbon::now()->format('Y-m-d');
        }
        $service = new GoogleAnalytics();
        $viewId = env('GA_VIEW_ID', '230760666'); // Must set

        $data = $service->crawlCampaigns($viewId, $fromTime, $toTime);
        if(!empty($data)) {
            foreach ($data as $camp_name => $v) {
                GaCampaignReports::updateOrCreate([
                    'campains_name' => $camp_name ?? '',
                    'view_id' => $v['view_id'] ?? $viewId,
                    'date_record'=> $v['date_record'] ?? $date_record,
                ], [
                    'users'=> $v['users'] ?? 0,
                    'new_users'=> $v['new_users'] ?? 0,
                    'session'=> $v['session'] ?? 0,
                    'bounce_rate'=> $v['bounce_rate'] ?? 0,
                    'pageviews_per_session'=> $v['pageviews_per_session'] ?? 0,
                    'avg_session_duration'=> $v['avg_session_duration'] ?? 0,
                    'goal_conversion_rate_all'=> $v['goal_conversion_rate_all'] ?? 0,
                    'goal_completions_all'=> $v['goal_completions_all'] ?? 0,
                    'goal_value_all'=> $v['goal_value_all'] ?? 0,
                ]);
            }
        };

        $this->info("Cron Job end at ". now());
        $this->info('Success!');
    }
}
