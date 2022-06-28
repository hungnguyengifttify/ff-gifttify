<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAdSets extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'campaign_id', 'account_id', 'name', 'status', 'configured_status', 'effective_status', 'daily_budget'
    ];
}
