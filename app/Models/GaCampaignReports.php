<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dashboard;

class GaCampaignReports extends Model
{
    use HasFactory;

    //Update list view
    static $viewIds = [
        'gift-us' => '253293522',
        'hippiessy.com' =>'272705190',
    ];

    public static function getViewIds() {
        $result = array();
        $allStore = Dashboard::getAllStoreConfig();
        foreach ($allStore as $store => $value) {
            $result[$store] = $value['google']['viewId'];
        }
        return $result;
    }

    public static function getViewTimezone() {
        $result = array();
        $allStore = Dashboard::getAllStoreConfig();
        foreach ($allStore as $store => $value) {
            $result[$store] = $value['common']['phpTimeZone'];
        }
        return $result;
    }

    public static function getStoreFromAccountId ($accountId) {
        $allStore = Dashboard::getAllStoreConfig();
        foreach ($allStore as $store => $value) {
            if ( in_array($accountId, $value['common']['fbAccountIds']) ) {
                return $store;
            }
        }
        return '';
    }

    protected $fillable = [
        'campaign_name', 'store', 'view_id', 'date_record', 'users', 'new_users', 'session',
        'bounce_rate', 'pageviews_per_session', 'avg_session_duration', 'transactions',
        'transactions_per_session', 'transaction_revenue', 'ad_cost'
    ];
}
