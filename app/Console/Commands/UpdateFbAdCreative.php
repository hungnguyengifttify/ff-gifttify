<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use Illuminate\Console\Command;
use FacebookAds\Logger\CurlLogger;

use App\Models\FbAds;
use App\Models\FbAdsCreatives;

class UpdateFbAdCreative extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fbads:update_ad_creative {time_report?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update FB Ad Creative';

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

        $fields = array (
            "account_id",
            "actor_id",
            "body",
            "call_to_action_type",
            "effective_object_story_id",
            "id",
            "image_crops",
            "image_hash",
            "image_url",
            "name",
            "object_story_spec",
            "object_type",
            "status",
            "thumbnail_url",
            "title",
            "url_tags",
        );

        foreach ($fbAccountIds as $accountId) {
            $timeReport = $this->argument('time_report');
            if ($timeReport == 'all') {
                $params = array('date_preset' => 'maximum');
            } else {
                $params = array('date_preset' => 'last_3d');
            }

            $cursor = (new AdAccount("act_$accountId"))->getAdCreatives(
                $fields,
                $params
            );
            do {
                $data = $cursor->getResponse()->getContent()['data'];
                foreach ($data as $v) {
                    FbAdsCreatives::updateOrCreate([
                        'id' => $v['id'] ?? 0,
                    ], [
                        'account_id' => $v['account_id'] ?? 0,
                        'actor_id' => $v['actor_id'] ?? 0,
                        'body' => $v['body'] ?? '',
                        'call_to_action_type' => $v['call_to_action_type'] ?? '',
                        'effective_object_story_id' => $v['effective_object_story_id'] ?? '',
                        'image_crops' => isset($v['image_crops']) ? json_encode($v['image_crops']) : json_encode(''),
                        'image_hash' => $v['image_hash'] ?? '',
                        'image_url' => $v['image_url'] ?? '',
                        'name' => $v['name'] ?? '',
                        'object_story_spec' => isset($v['object_story_spec']) ? json_encode($v['object_story_spec']) : json_encode(''),
                        'object_type' => $v['object_type'] ?? '',
                        'status' => $v['status'] ?? '',
                        'thumbnail_url' => $v['thumbnail_url'] ?? '',
                        'title' => $v['title'] ?? '',
                        'url_tags' => $v['url_tags'] ?? '',
                    ]);
                }
                //$cursor->fetchAfter();
                break;
            } while (empty($data) === false);
        }

        $this->info("Cron Job End at ". now());
        $this->info('Success!');
    }
}
