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
        '977262739875449'
    );

    static $auAccountIds = array(
        '209267284523548',
        '4065060523598849',
        '199757128777881',
        '619094789457793',
        '333511255213931',
    );

    static $deAccountIds = array(
        '697732767946862'
    );

    protected $fillable = [
        'id', 'account_id', 'adset', 'adset_id', 'bid_type', 'campaign', 'campaign_id',
        'configured_status', 'conversion_domain', 'conversion_specs', 'created_time',
        'creative', 'creative_id', 'demolink_hash', 'display_sequence', 'effective_status', 'engagement_audience',
        'last_updated_by_app_id', 'name', 'preview_shareable_link', 'source_ad',
        'source_ad_id', 'status', 'targeting', 'tracking_and_conversion_with_defaults',
        'tracking_specs', 'updated_time'
    ];

}
