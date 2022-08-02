<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAds extends Model
{
    use HasFactory;

    static $usAccountIds = array(
        '2978647975730170',
        '612291543439190',
        '309846854338542',
        '588068822423832',
        '651874502834964',
        '748598509494241',
        '1038512286982822',
        '300489508749827',
        '977262739875449',
        //'737747440975590',
        //'1056823075169563',
        '786388902366696',
        '1004611960231517'
    );

    static $auThecreattifyAccountIds = array(
        '209267284523548',
        '4065060523598849',
        '199757128777881',
        '619094789457793',
        '333511255213931',
    );

    static $singlecloudyAccountIds = array(
        '777279280077834',
        '1082447066006031',
        '598338885251308',
        '5136799149773518',
        '1197633801022302',
        '615804839944246',
        '2913344045636158'
    );

    public static function getAllRunningAccountIds() {
        return array_merge(
            FbAds::$usAccountIds,
            FbAds::$auThecreattifyAccountIds,
            FbAds::$singlecloudyAccountIds
        );
    }

    public static function getPhpDateTimeZoneByAccountId($accountId) {
        $dateTimeZone = "";
        if (in_array($accountId, FbAds::$usAccountIds)) {
            $dateTimeZone = new \DateTimeZone('America/Los_Angeles');
        } elseif (in_array($accountId, FbAds::$auThecreattifyAccountIds)) {
            $dateTimeZone = new \DateTimeZone('Australia/Sydney');
        } elseif (in_array($accountId, FbAds::$singlecloudyAccountIds)) {
            $dateTimeZone = new \DateTimeZone('America/Los_Angeles');
        }
        return $dateTimeZone;
    }

    protected $fillable = [
        'id', 'account_id', 'adset', 'adset_id', 'bid_type', 'campaign', 'campaign_id',
        'configured_status', 'conversion_domain', 'conversion_specs', 'created_time',
        'creative', 'creative_id', 'demolink_hash', 'display_sequence', 'effective_status', 'engagement_audience',
        'last_updated_by_app_id', 'name', 'preview_shareable_link', 'source_ad',
        'source_ad_id', 'status', 'targeting', 'tracking_and_conversion_with_defaults',
        'tracking_specs', 'updated_time'
    ];

}
