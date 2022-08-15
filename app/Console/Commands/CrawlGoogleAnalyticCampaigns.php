<?php

namespace App\Console\Commands;

use App\Services\GoogleAnalytics;
use Illuminate\Console\Command;
use Carbon\Carbon;
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

        $fromTime = $this->argument('from_time') ?? Carbon::now()->format('Y-m-d');
        $toTime = $this->argument('to_time') ?? $fromTime;
        if($fromTime > $toTime){
            $this->info("Error: from_time > to_time.");
            return false;
        };

        $dateRange = $this->dateRange($fromTime, $toTime);
        $viewIds = GaCampaignReports::$viewIds; // MUST SET
        if(count($viewIds) < 1){
            $this->info("Error: View id not set.");
            return false;
        };

        $service = new GoogleAnalytics();
        foreach ($viewIds as $site => $viewId) {
            foreach ($dateRange as  $date_record) {
                $this->info("Cron Job crawl date ". ' view ' . $viewId . '(' . $site .') '. $date_record );
                $data = $service->crawlCampaigns($viewId, $date_record, $date_record);
                if (empty($data)) {
                    continue;
                }

                foreach ($data as $camp_name => $v) {
                    GaCampaignReports::updateOrCreate([
                        'campains_name' => $camp_name,
                        'view_id' => $viewId,
                        'date_record' => $date_record,
                    ], [
                        'users' => $v['users'] ?? 0,
                        'new_users' => $v['new_users'] ?? 0,
                        'session' => $v['session'] ?? 0,
                        'bounce_rate' => $v['bounce_rate'] ?? 0,
                        'pageviews_per_session' => $v['pageviews_per_session'] ?? 0,
                        'avg_session_duration' => $v['avg_session_duration'] ?? 0,
                        'transactions' => $v['transactions'] ?? 0,
                        'transactions_per_session' => $v['transactions_per_session'] ?? 0,
                        'transaction_revenue' => $v['transaction_revenue'] ?? 0,
//                        'goal_conversion_rate_all' => $v['goal_conversion_rate_all'] ?? 0,
//                        'goal_completions_all' => $v['goal_completions_all'] ?? 0,
//                        'goal_value_all' => $v['goal_value_all'] ?? 0,
                    ]);
                }
            }
        }

        $this->info("Cron Job end at ". now());
        $this->info('Success!');
    }

    function dateRange($first, $last, $step = '+1 day', $format = 'Y-m-d')
    {
        $dates = [];
        $current = \DateTime::createFromFormat($format, $first)->getTimestamp();
        $last = \DateTime::createFromFormat($format, $last)->getTimestamp();

        while ($current <= $last) {
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }
}
