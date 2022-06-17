<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbCampaignInsights extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id', 'account_name', 'account_currency', 'ad_id', 'ad_name', 'adset_id', 'adset_name',
        'campaign_id', 'campaign_name', 'country', 'cpc', 'cpm', 'cpp', 'ctr', 'date_record',
        'impressions', 'objective', 'reach', 'spend', 'inline_link_clicks', 'unique_clicks',
        'unique_link_clicks_ctr', 'clicks'
    ];
}
