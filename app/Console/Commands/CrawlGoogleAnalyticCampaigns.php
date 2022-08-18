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

        $viewIds = GaCampaignReports::getViewIds(); // MUST SET
        if(count($viewIds) < 1){
            $this->info("Error: View id not set.");
            return false;
        };

        $service = new GoogleAnalytics();
        foreach ($viewIds as $site => $viewId) {
            $timezone = GaCampaignReports::getViewTimezone();

            $datetimezone = $timezone[$site] ?? '';
            $datetimezone = new \DateTimeZone($datetimezone);
            $fromTime = $this->argument('from_time') ?? Carbon::now($datetimezone)->subDays(2)->format('Y-m-d');
            $toTime = $this->argument('to_time') ?? Carbon::now($datetimezone)->format('Y-m-d');
            if($fromTime > $toTime){
                $this->info("Error: from_time > to_time.");
                continue;
            };

            $dateRange = $this->dateRange($fromTime, $toTime, $timezone[$site]);

            foreach ($dateRange as  $date_record) {
                $this->info("Cron Job crawl date ". ' view ' . $viewId . '(' . $site .') '. $date_record );
                $data = $service->crawlCampaigns($viewId, $date_record, $date_record);
                if (empty($data)) {
                    continue;
                }

                foreach ($data as $camp_name => $v) {
                    GaCampaignReports::updateOrCreate([
                        'campaign_name' => $camp_name,
                        'store' => $site ?? '',
                        'date_record' => $date_record,
                    ], [
                        'view_id' => $viewId,
                        'users' => $v['users'] ?? 0,
                        'new_users' => $v['new_users'] ?? 0,
                        'session' => $v['session'] ?? 0,
                        'bounce_rate' => $v['bounce_rate'] ?? 0,
                        'pageviews_per_session' => $v['pageviews_per_session'] ?? 0,
                        'avg_session_duration' => $v['avg_session_duration'] ?? 0,
                        'transactions' => $v['transactions'] ?? 0,
                        'transactions_per_session' => $v['transactions_per_session'] ?? 0,
                        'transaction_revenue' => $v['transaction_revenue'] ?? 0,
                        'ad_cost' => $v['ad_cost'] ?? 0
                    ]);
                }
            }
        }

        $this->info("Cron Job end at ". now());
        $this->info('Success!');
    }

    function dateRange($first, $last, $timezone = 'America/Los_Angeles')
    {
        $timezone = new \DateTimeZone($timezone);
        $format = 'Y-m-d';
        $step = 1;

        $begin = \DateTime::createFromFormat($format, $first, $timezone);
        $end = \DateTime::createFromFormat($format, $last, $timezone);
        $end = $end->modify( '+1 day' );

        $interval = new \DateInterval("P{$step}D");
        $dateRange = new \DatePeriod($begin, $interval ,$end);

        $gTimeRanges = array();
        foreach($dateRange as $date){
            $gTimeRanges[] = $date->format("Y-m-d");
        }
        return $gTimeRanges;
    }
}
