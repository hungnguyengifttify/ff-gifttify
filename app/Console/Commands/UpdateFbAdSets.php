<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use Illuminate\Console\Command;

use App\Models\FbAds;
use App\Models\FbAdSets;

class UpdateFbAdSets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fbads:update_adsets {time_report?} {account_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update FB Ad Sets';

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
            'campaign_id',
            'account_id',
            'daily_budget',
            'status',
            'configured_status',
            'effective_status',
        );

        foreach ($fbAccountIds as $accountId) {
            $timeReport = $this->argument('time_report');
            if ($timeReport == 'all') {
                $params = array('date_preset' => 'maximum');
            } else {
                $params = array('date_preset' => 'last_3d', 'limit' => 200);
            }

            $cursor = (new AdAccount("act_$accountId"))->getAdSets(
                $fields,
                $params
            );
            do {
                $data = $cursor->getResponse()->getContent()['data'];
                foreach ($data as $v) {
                    FbAdSets::updateOrCreate([
                        'id' => $v['id'] ?? 0,
                    ], [
                        'name' => $v['name'] ?? '',
                        'account_id' => $v['account_id'] ?? 0,
                        'campaign_id' => $v['campaign_id'] ?? 0,
                        'daily_budget' => $v['daily_budget'] ?? 0,
                        'status' => $v['status'] ?? '',
                        'configured_status' => $v['configured_status'] ?? '',
                        'effective_status' => $v['effective_status'] ?? '',
                    ]);
                }
                //$cursor->fetchAfter();
                break;
            } while (empty($data) === false);
            sleep(10);
        }

        $this->info("Cron Job End at ". now());
        $this->info('Success!');
    }
}
