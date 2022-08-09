<?php

namespace App\Console\Commands;

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\AdAccount;
use Illuminate\Console\Command;
use App\Models\FbAds;
use App\Models\FbAdsInsights;

class UpdateFbAdsInsights extends Command
{
    /**
     * The name and signature of the console command.
     * time ['all', 'today']
     * @var string
     */
    protected $signature = 'fbads:update_ads_insights {time_report?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Facebook Ads Insights';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job running at ". now());
        $fbAccountIds = FbAds::getAllRunningAccountIds();

        $access_token = env('FB_ADS_ACCESS_TOKEN', '');
        $app_secret = env('FB_ADS_APP_SECRET', '');
        $app_id = env('FB_ADS_APP_ID', '');

        $api = Api::init($app_id, $app_secret, $access_token);
        $api->setDefaultGraphVersion('14.0');
        //$api->setLogger(new CurlLogger());

        $fields = array(
            'cost_per_unique_click',
            'account_currency',
            'account_id',
            'account_name',
            'ad_id',
            'ad_name',
            'adset_id',
            'adset_name',
            'campaign_id',
            'campaign_name',
            'cpc',
            'cpm',
            'cpp',
            'ctr',
            'date_start',
            'date_stop',
            'impressions',
            'labels',
            'location',
            'objective',
            'reach',
            'social_spend',
            'spend',
            'inline_link_clicks',
            'unique_clicks',
            'reach',
            'unique_link_clicks_ctr',
            'clicks',
        );
        $params = array(
            'level' => 'ad',
            'breakdowns' => array('country'),
        );

        foreach ($fbAccountIds as $accountId) {
            $dateTimeZone = FbAds::getPhpDateTimeZoneByAccountId($accountId);
            $timeReport = $this->argument('time_report');

            $begin = new \DateTime("now", $dateTimeZone);
            if ($timeReport == 'all') {
                $begin = $begin->modify( '-10 day' );
            } else {
                $begin = $begin->modify( '-1 day' );
            }

            $end = new \DateTime("now", $dateTimeZone);
            $end = $end->modify( '+1 day' );

            $interval = new \DateInterval('P1D');
            $dateRange = new \DatePeriod($begin, $interval ,$end);

            $fbTimeRanges = array();
            foreach($dateRange as $date){
                $fbTimeRanges[] = array('since' => $date->format("Y-m-d"),'until' => $date->format("Y-m-d"));
            }
            $params['time_ranges'] = $fbTimeRanges;

            $cursor = (new AdAccount("act_$accountId"))->createCampaign(
                $fields,
                $params
            );
            do {
                $data = $cursor->getResponse()->getContent()['data'];
                foreach ($data as $v) {
                    FbAdsInsights::updateOrCreate([
                        'ad_id' => $v['ad_id'] ?? 0,
                        'adset_id' => $v['adset_id'] ?? 0,
                        'campaign_id' => $v['campaign_id'] ?? 0,
                        'country' => $v['country'] ?? '',
                        'date_record' => $v['date_start'] ?? '',
                    ], [
                        'account_id' => $v['account_id'] ?? 0,
                        'account_name' => $v['account_name'] ?? '',
                        'account_currency' => $v['account_currency'] ?? '',
                        'ad_name' => $v['ad_name'] ?? '',
                        'adset_name' => $v['adset_name'] ?? '',
                        'campaign_name' => $v['campaign_name'],
                        'cpc' => $v['cpc'] ?? 0,
                        'cpm' => $v['cpm'] ?? 0,
                        'cpp' => $v['cpp'] ?? 0,
                        'ctr' => $v['ctr'] ?? 0,
                        'impressions' => $v['impressions'] ?? 0,
                        'objective' => $v['objective'] ?? '',
                        'reach' => $v['reach'] ?? 0,
                        'spend' => $v['spend'] ?? 0,
                        'inline_link_clicks' => $v['inline_link_clicks'] ?? 0,
                        'unique_clicks' => $v['unique_clicks'] ?? 0,
                        'unique_link_clicks_ctr' => $v['unique_link_clicks_ctr'] ?? 0,
                        'clicks' => $v['clicks'] ?? 0,
                    ]);
                }
                $cursor->fetchAfter();
            } while (empty($data) === false);

        }

        $this->info("Cron Job end at ". now());
        $this->info('Success!');
    }
}
