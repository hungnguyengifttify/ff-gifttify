<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAccount extends Model
{
    use HasFactory;

    static $status = array(
        1 => 'ACTIVE',
        2 => 'DISABLED',
        3 => 'UNSETTLED',
        7 => 'PENDING_RISK_REVIEW',
        8 => 'PENDING_SETTLEMENT',
        9 => 'IN_GRACE_PERIOD',
        100 => 'PENDING_CLOSURE',
        101 => 'CLOSED',
        201 => 'ANY_ACTIVE',
        202 => 'ANY_CLOSED',
    );

    static $disable_reason = array(
        0 => 'NONE',
        1 => 'ADS_INTEGRITY_POLICY',
        2 => 'ADS_IP_REVIEW',
        3 => 'RISK_PAYMENT',
        4 => 'GRAY_ACCOUNT_SHUT_DOWN',
        5 => 'ADS_AFC_REVIEW',
        6 => 'BUSINESS_INTEGRITY_RAR',
        7 => 'PERMANENT_CLOSE',
        8 => 'UNUSED_RESELLER_ACCOUNT',
        9 => 'UNUSED_ACCOUNT',
        10 => 'UMBRELLA_AD_ACCOUNT',
        11 => 'BUSINESS_MANAGER_INTEGRITY_POLICY',
        12 => 'MISREPRESENTED_AD_ACCOUNT',
        13 => 'AOAB_DESHARE_LEGAL_ENTITY',
        14 => 'CTX_THREAD_REVIEW',
        15 => 'COMPROMISED_AD_ACCOUNT',
    );

    protected $fillable = [
        'id', 'account_act_id', 'name', 'account_status', 'store', 'age', 'amount_spent',
        'balance', 'currency', 'disable_reason', 'end_advertiser', 'end_advertiser_name',
        'min_campaign_group_spend_cap', 'min_daily_budget', 'owner', 'spend_cap', 'timezone_id',
        'timezone_name', 'timezone_offset_hours_utc', 'business_city', 'business_country_code',
        'business_name', 'business_state', 'business_street', 'business_street2', 'business_zip',
        'created_time'
    ];

}
