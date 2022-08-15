<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GaCampaignReports extends Model
{
    use HasFactory;

    //Update list view
    static $viewIds = [
        'gift-us' => '253293522',
        'hippiessy.com' =>'272705190',
    ];

    protected $fillable = [
        'campains_name', 'view_id', 'date_record', 'users', 'new_users', 'session', 'bounce_rate', 'pageviews_per_session', 'avg_session_duration', 'transactions', 'transactions_per_session', 'transaction_revenue'
    ];
}
