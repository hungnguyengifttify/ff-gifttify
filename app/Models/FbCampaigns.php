<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbCampaigns extends Model
{
    use HasFactory;

    protected $fillable = [
        'fb_campaign_id', 'name', 'account_id', 'daily_budget', 'budget_remaining',
        'status', 'start_time', 'updated_time', 'bid_strategy', 'configured_status',
        'effective_status', 'objective', 'buying_type', 'special_ad_category'
    ];
}
