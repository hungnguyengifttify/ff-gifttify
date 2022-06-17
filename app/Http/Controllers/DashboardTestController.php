<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FbAds;

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\AdsInsights;

class DashboardTestController extends Controller {

    public function index(Request $request)
    {
        $access_token = 'EAAIa3A2G9ykBAIddfZBrAX9TrHaaJiWFUxJlTCFhRUl1rMwexv64TvjuSIQWtVxY1NMXDS05ujXhFamTnSNkrj70A37JhF9TeZCi9iXYZCPzZASoMST5cEGouqGl1prPwaEHhkZBHPZC4eJZBO6D6g8yQr210SaCRCQEEJpHMFVHGEXUEF7oSHFi518UoZC2I7KPidJSoELZBsAZDZD';
        $ad_account_id = 'act_334978668640347';
        $app_secret = '516a00449114697582df9e2c4b18610b';
        $app_id = '592482375497513';

        $api = Api::init($app_id, $app_secret, $access_token);
        $api->setDefaultGraphVersion('14.0');
        $api->setLogger(new CurlLogger());

        $fields = array(
            'cost_per_unique_click',
            'account_currency',
            'account_id',
            'account_name',
            'ad_id',
            'ad_name',
            'adset_id',
            'adset_name',
            'campaign_id',
            'campaign_name',
            'cpc',
            'cpm',
            'cpp',
            'created_time',
            'ctr',
            'date_start',
            'date_stop',
            'gender_targeting',
            'impressions',
            'labels',
            'location',
            'objective',
            'reach',
            'social_spend',
            'spend',
            'updated_time',
            'inline_link_clicks',
            'outbound_clicks',
            'unique_clicks',
            'reach',
            'unique_actions',
            'unique_link_clicks_ctr',
            'clicks',
            'cost_per_unique_action_type',
            'actions',
            'purchase_roas',
            'website_purchase_roas'
        );
        $params = array(
            'time_ranges' => array(
                array('since' => '2022-06-15','until' => '2022-06-15'),
                array('since' => '2022-06-16','until' => '2022-06-16'),
                array('since' => '2022-06-17','until' => '2022-06-17'),
            ),
            'level' => 'campaign',
            'breakdowns' => array('country'),
        );

        $result = (new AdAccount('act_209267284523548'))->getInsights(
            $fields,
            $params
        )->getResponse()->getContent();

        dd($result);


        $title = "Test Dashboard";
        $fromDate = $request->date('fromDate') ? $request->date('fromDate')->format('Y-m-d') : date('Y-m-d');
        $toDate = $request->date('toDate') ? $request->date('toDate')->format('Y-m-d 23:59:59') : date('Y-m-d 23:59:59');
        $labelDate = $request->input('labelDate') ?? 'Today';

        $params = array(
            'fromDate' => new \DateTime($fromDate),
            'toDate' => new \DateTime($toDate),
            'labelDate' => $labelDate,
        );

        $fbAds = DB::table('facebook_ads_ad_insights')
            ->select(DB::raw('sum(spend) as totalSpend'))
            ->whereIn('account_name', FbAds::$usAccount)
            ->where('account_name', 'not like', 'Phong%')
            ->where('date_start', '>=', $fromDate)
            ->where('date_stop', '<=', $toDate)
            ->first();

        $orders = DB::select("select * from shopify_thecreattify_orders where CONVERT_TZ(created_at,'UTC','US/Pacific') >= :fromDate and CONVERT_TZ(created_at,'UTC','US/Pacific') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);
        return view('dashboard', compact('title', 'params', 'orders', 'fbAds'));
    }
}
