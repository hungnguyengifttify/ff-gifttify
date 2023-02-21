<?php

namespace App\Console\Commands;

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\AdAccount;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\FbAds;
use App\Models\FbCampaigns;

class UpdateFbCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     * time ['all', 'today']
     * @var string
     */
    protected $signature = 'fbads:update_campaigns {time_report?} {account_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Facebook Campaigns';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $account_id = $this->argument('account_id') ?? '';
        $fbAccountIds = FbAds::getAllRunningAccountIds();
        if ($account_id) {
            $fbAccountIds = array($account_id);
        }
        $this->info("Cron Job $account_id running at ". now());

        $access_token = env('FB_ADS_ACCESS_TOKEN', '');
        $app_secret = env('FB_ADS_APP_SECRET', '');
        $app_id = env('FB_ADS_APP_ID', '');

        $api = Api::init($app_id, $app_secret, $access_token);
        $api->setDefaultGraphVersion('14.0');
        //$api->setLogger(new CurlLogger());

        $fields = array (
            'id',
            'name',
            'account_id',
            'daily_budget',
            'budget_remaining',
            'start_time',
            'status',
            'updated_time',
            'bid_strategy',
            'configured_status',
            'effective_status',
            'objective',
            'buying_type',
            'special_ad_category',
        );

        foreach ($fbAccountIds as $accountId) {
            $timeReport = $this->argument('time_report');
            if ($timeReport == 'all') {
                $params = array('date_preset' => 'maximum');
            } else {
                $params = array('date_preset' => 'last_3d');
            }

            $cursor = (new AdAccount("act_$accountId"))->getCampaigns(
                $fields,
                $params
            );
            do {
                $data = $cursor->getResponse()->getContent()['data'];
                foreach ($data as $v) {
                    FbCampaigns::updateOrCreate([
                        'fb_campaign_id' => $v['id'] ?? 0,
                    ], [
                        'name' => $v['name'] ?? '',
                        'account_id' => $v['account_id'] ?? 0,
                        'daily_budget' => $v['daily_budget'] ?? 0,
                        'budget_remaining' => $v['budget_remaining'] ?? 0,
                        'status' => $v['status'] ?? '',
                        'start_time' => Carbon::createFromFormat(\DateTime::ISO8601, $v['start_time'], 'UTC') ?? '1900-01-01',
                        'updated_time' => Carbon::createFromFormat(\DateTime::ISO8601, $v['updated_time'], 'UTC') ?? '1900-01-01',
                        'bid_strategy' => $v['bid_strategy'] ?? '',
                        'configured_status' => $v['configured_status'] ?? '',
                        'effective_status' => $v['effective_status'] ?? '',
                        'buying_type' => $v['buying_type'] ?? '',
                        'special_ad_category' => $v['special_ad_category'] ?? '',
                    ]);
                }
                $cursor->fetchAfter();
                sleep(10);
            } while (empty($data) === false);
            sleep(10);
        }

        $this->info("Cron Job end at ". now());
        $this->info('Success!');
    }
}
