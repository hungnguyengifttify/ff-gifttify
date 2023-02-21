<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use Illuminate\Console\Command;
use FacebookAds\Logger\CurlLogger;

use App\Models\FbAds;
use App\Models\FbAdsCreatives;

class UpdateFbAds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fbads:update_ads {time_report?} {account_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update FB Ads';

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
            "account_id",
            "ad_review_feedback",
            "adlabels",
            "adset",
            "adset_id",
            "bid_amount",
            "bid_info",
            "bid_type",
            "campaign",
            "campaign_id",
            "configured_status",
            "conversion_domain",
            "conversion_specs",
            "created_time",
            "creative",
            "demolink_hash",
            "display_sequence",
            "effective_status",
            "engagement_audience",
            "failed_delivery_checks",
            "id",
            "issues_info",
            "last_updated_by_app_id",
            "name",
            "preview_shareable_link",
            "priority",
            "recommendations",
            "source_ad",
            "source_ad_id",
            "status",
            "targeting",
            "tracking_and_conversion_with_defaults",
            "tracking_specs",
            "updated_time",
            "adset_spec",
            "audience_id",
            "date_format",
            "draft_adgroup_id",
            "execution_options",
            "include_demolink_hashes",
            "filename",
        );

        foreach ($fbAccountIds as $accountId) {
            $timeReport = $this->argument('time_report');
            if ($timeReport == 'all') {
                $params = array('date_preset' => 'maximum');
            } else {
                $params = array('date_preset' => 'last_3d');
            }

            $cursor = (new AdAccount("act_$accountId"))->getAds(
                $fields,
                $params
            );
            do {
                $data = $cursor->getResponse()->getContent()['data'];
                foreach ($data as $v) {
                    FbAds::updateOrCreate([
                        'id' => $v['id'] ?? 0,
                    ], [
                        'account_id' => $v['account_id'] ?? 0,
                        'adset' => isset($v['adset']) ? json_encode($v['adset']) : json_encode(''),
                        'adset_id' => $v['adset_id'] ?? 0,
                        'bid_type' => $v['bid_type'] ?? '',
                        'campaign' => isset($v['campaign']) ? json_encode($v['campaign']) : json_encode(''),
                        'campaign_id' => $v['campaign_id'] ?? 0,
                        'configured_status' => $v['configured_status'] ?? '',
                        'conversion_domain' => $v['conversion_domain'] ?? '',
                        'conversion_specs' => isset($v['conversion_specs']) ? json_encode($v['conversion_specs']) : json_encode(''),
                        'created_time' => Carbon::createFromFormat(\DateTime::ISO8601, $v['created_time'], 'UTC') ?? '1900-01-01',
                        'updated_time' => Carbon::createFromFormat(\DateTime::ISO8601, $v['updated_time'], 'UTC') ?? '1900-01-01',
                        'creative' => isset($v['creative']) ? json_encode($v['creative']) : json_encode(''),
                        'creative_id' => $v['creative']['id'] ?? 0,
                        'demolink_hash' => $v['demolink_hash'] ?? '',
                        'display_sequence' => $v['display_sequence'] ?? 0,
                        'effective_status' => $v['effective_status'] ?? '',
                        'engagement_audience' => $v['engagement_audience'] ?? 0,
                        'last_updated_by_app_id' => $v['last_updated_by_app_id'] ?? '',
                        'name' => $v['name'] ?? '',
                        'preview_shareable_link' => $v['preview_shareable_link'] ?? '',
                        'source_ad' => isset($v['source_ad']) ? json_encode($v['source_ad']) : json_encode(''),
                        'source_ad_id' => $v['source_ad_id'] ?? 0,
                        'status' => $v['status'] ?? '',
                        'targeting' => isset($v['targeting']) ? json_encode($v['targeting']) : json_encode(''),
                        'tracking_and_conversion_with_defaults' => isset($v['tracking_and_conversion_with_defaults']) ? json_encode($v['tracking_and_conversion_with_defaults']) : json_encode(''),
                        'tracking_specs' => isset($v['tracking_specs']) ? json_encode($v['tracking_specs']) : json_encode(''),

                    ]);
                }
                if ($timeReport != 'all') {
                    $cursor->fetchAfter();
                    break;
                }
                sleep(10);
            } while (empty($data) === false);
            sleep(10);
        }

        $this->info("Cron Job End at ". now());
        $this->info('Success!');
    }
}
