<?php

namespace App\Console\Commands;

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\AdAccount;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\FbAds;
use App\Models\FbCampaigns;
use FacebookAds\Object\Fields\AdAccountFields;

class TestCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cmd {time_report?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Google Drive files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job running at ". now());
        $fbAccountIds = array(
            '2978647975730170',
            '612291543439190',
            '309846854338542',
            '588068822423832',
            '651874502834964',
            //'748598509494241',
            '1038512286982822',
            //'300489508749827',
            //'977262739875449',
            //'1056823075169563',
            '786388902366696',
            '1004611960231517',
            '1101927910729121',
            '737747440975590',
            '348916160782979',
            '760002925207268',
            '3342632042729233'
        );

        $access_token = env('FB_ADS_ACCESS_TOKEN', '');
        $app_secret = env('FB_ADS_APP_SECRET', '');
        $app_id = env('FB_ADS_APP_ID', '');

        $api = Api::init($app_id, $app_secret, $access_token);
        $api->setDefaultGraphVersion('14.0');
        //$api->setLogger(new CurlLogger());

        $fields = array (
            "account_id",
            "account_status",
            "age",
            "agency_client_declaration",
            "amount_spent",
            "attribution_spec",
            "balance",
            "business",
            "business_city",
            "business_country_code",
            "business_name",
            "business_state",
            "business_street",
            "business_street2",
            "business_zip",
            //"capabilities",
            "created_time",
            "currency",
            "disable_reason",
            "end_advertiser",
            "end_advertiser_name",
            "failed_delivery_checks",
            "fb_entity",
            "funding_source",
            "funding_source_details",
            "has_advertiser_opted_in_odax",
            "has_migrated_permissions",
            "id",
            "io_number",
            "is_attribution_spec_system_default",
            "is_direct_deals_enabled",
            "is_in_3ds_authorization_enabled_market",
            "is_notifications_enabled",
            "is_personal",
            "is_prepay_account",
            "is_tax_id_required",
            "line_numbers",
            "media_agency",
            "min_campaign_group_spend_cap",
            "min_daily_budget",
            "name",
            //"offsite_pixels_tos_accepted",
            "owner",
            "partner",
            //"rf_spec",
            "spend_cap",
            "tax_id",
            "tax_id_status",
            "tax_id_type",
            "timezone_id",
            "timezone_name",
            "timezone_offset_hours_utc",
            "tos_accepted",
            "user_tasks",
            "user_tos_accepted"
        );

        foreach ($fbAccountIds as $accountId) {
            $cursor = (new AdAccount("act_$accountId"))->read($fields);
            $data = $cursor->getData();
            dump(
                $accountId,
                $data['id'],
                $data['account_status'],
                $data['business_country_code'],
                $data['balance'],
                $data['amount_spent'],
                $data['timezone_name'],
                $data['min_daily_budget'],
                $data['name'],
                '_________________________'

            );
            //sleep(10);
        }

        $this->info("Cron Job end at ". now());
        $this->info('Success!');
    }
}
