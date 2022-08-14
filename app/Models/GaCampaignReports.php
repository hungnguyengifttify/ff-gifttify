<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GaCampaignReports extends Model
{
    use HasFactory;

    protected $fillable = [
        'campains_name', 'view_id', 'date_record', 'users', 'new_users', 'session', 'bounce_rate', 'pageviews_per_session', 'avg_session_duration', 'goal_conversion_rate_all', 'goal_completions_all', 'goal_value_all'
    ];
}
