<?php

namespace App\Console\Commands;

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\AdAccount;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\FbAds;
use App\Models\FbAccount;
use App\Models\FbCampaigns;

class UpdateFbAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fbacc:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update FB Account';

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
            if ($accountId == 0) continue;

            $cursor = (new AdAccount("act_$accountId"))->read($fields);
            $v = $cursor->getData();

            FbAccount::updateOrCreate([
                'id' => $v['account_id'] ?? 0,
            ], [
                'account_act_id' => $v['id'] ?? '',
                'name' => $v['name'] ?? '',
                'account_status' => $v['account_status'] ?? 0,
                'store' => $v['store'] ?? '',
                'age' => $v['age'] ?? 0,
                'amount_spent' => $v['amount_spent'] ?? 0,
                'balance' => $v['balance'] ?? 0,
                'currency' => $v['currency'] ?? '',
                'disable_reason' => $v['disable_reason'] ?? 0,
                'end_advertiser' => $v['end_advertiser'] ?? 0,
                'end_advertiser_name' => $v['end_advertiser_name'] ?? '',
                'min_campaign_group_spend_cap' => $v['min_campaign_group_spend_cap'] ?? 0,
                'min_daily_budget' => $v['min_daily_budget'] ?? 0,
                'owner' => $v['owner'] ?? 0,
                'spend_cap' => $v['spend_cap'] ?? 0,
                'timezone_id' => $v['timezone_id'] ?? 0,
                'timezone_name' => $v['timezone_name'] ?? '',
                'timezone_offset_hours_utc' => $v['timezone_offset_hours_utc'] ?? 0,
                'business_city' => $v['business_city'] ?? '',
                'business_country_code' => $v['business_country_code'] ?? '',
                'business_name' => $v['business_name'] ?? '',
                'business_state' => $v['business_state'] ?? '',
                'business_street' => $v['business_street'] ?? '',
                'business_street2' => $v['business_street2'] ?? '',
                'business_zip' => $v['business_zip'] ?? '',
                'created_time' => Carbon::createFromFormat(\DateTime::ISO8601, $v['created_time'], 'UTC') ?? '1900-01-01',
            ]);
        }

        $this->info("Cron Job end at ". now());
        $this->info('Success!');
    }
}
