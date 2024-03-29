<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAds extends Model
{
    use HasFactory;

    static $thecreattifyAccountIds = array(
//        '588068822423832',
        //'748598509494241',
        //'1038512286982822',
        //'300489508749827',
        //'977262739875449',
        //'1056823075169563',
        //'786388902366696',
        //'1004611960231517',
//        '1101927910729121',
        '737747440975590',
        //'760002925207268',
        //'3342632042729233',
        //'8233760193308267',
        //'821019145735527',
        //'632481991577703',
        //'8216411698399207',
        //'1053060378730837',
        //'1132980897291344',
        //'512538297409930',
        //'837499343929005',
        '853776385757197',
        '3167382426855198',
        '518957820097495',
        '463912435728671',
        '5603127019778899',
        '2177732719064623',
        '645297163894763',
        '1079560756058950',
        '811680439961950',
        '460612252725985',
        '824161118540565',
        '235842881979118',
        '589182285704295',
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
        '2913344045636158',
        //'602169491487389',
        //'5572131709473851',
        //'559711022572539',
    );

    static $gifttifyusAccountIds = array(
        //'600026628197610',
        //'1366470850544115',
//        '612291543439190',
        //'605826157700629',
        //'2978647975730170',
        //'348916160782979',
        '694986398712068',
        '633581741455903',
        '3267045873569068',
        '467298532056565',
        '488728306554917',
        '631216228737636',
    );

    static $owllifyAccountIds = array(
        //'756641988792285',
    );

    static $storeGifttifyAccountIds = array(
        //'1320735945000228'
    );

    static $hippiesyAccountIds = array(
        //'1103239003626786',
        //'412049937660963',
        //'783889102810922',
        //'1660860257624794',
    );

    static $getcusAccountIds = array(
        //'821019145735527',
        //'798400701180976',
        '818030216171071',
        //'298584852478230',
        '449216250519362',
        '5894176903927139',
        '1028900394445997',
        '651874502834964',
        '309846854338542',
    );

    static $whelandsAccountIds = array(
        '1131310947769505',
    );

    static $ipAccountIds = array(
        '460612252725985',
    );

    static $lumelightartAccountIds = array(
        '824161118540565'
    );

    public static function getAllRunningAccountIds() {
        return array_merge(
            FbAds::$lumelightartAccountIds,
            FbAds::$ipAccountIds,
            FbAds::$thecreattifyAccountIds,
            //FbAds::$auThecreattifyAccountIds,
            //FbAds::$singlecloudyAccountIds,
            FbAds::$gifttifyusAccountIds,
            FbAds::$owllifyAccountIds,
            FbAds::$storeGifttifyAccountIds,
            FbAds::$hippiesyAccountIds,
            FbAds::$getcusAccountIds,
            FbAds::$whelandsAccountIds
        );
    }

    public static function getPhpDateTimeZoneByAccountId($accountId) {
        $dateTimeZone = new \DateTimeZone('America/Los_Angeles');
        if (in_array($accountId, FbAds::$whelandsAccountIds)) {
            $dateTimeZone = new \DateTimeZone('Australia/Sydney');
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
