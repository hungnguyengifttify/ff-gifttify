<?php

namespace App\Console\Commands;

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\AdAccount;
use Illuminate\Console\Command;

class UpdateFbCampaignInsights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fbads:update_campaign_insights';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Facebook Campaign Insights';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
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
            'time_ranges' => array(
                array('since' => '2022-06-15','until' => '2022-06-15'),
                array('since' => '2022-06-16','until' => '2022-06-16'),
                array('since' => '2022-06-17','until' => '2022-06-17'),
            ),
            'level' => 'campaign',
            'breakdowns' => array('country'),
        );

        $result = (new AdAccount('act_209267284523548'))->getInsights(
            $fields,
            $params
        )->getResponse()->getContent();

        dd($result);

        $this->info('Success!');

        dd($result);
    }
}
