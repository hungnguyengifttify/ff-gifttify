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
        $access_token = env('FB_ADS_ACCESS_TOKEN', '');
        $app_secret = env('FB_ADS_APP_SECRET', '');
        $app_id = env('FB_ADS_APP_ID', '');

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
            'ctr',
            'date_start',
            'date_stop',
            'impressions',
            'labels',
            'location',
            'objective',
            'reach',
            'social_spend',
            'spend',
            'inline_link_clicks',
            'outbound_clicks',
            'unique_clicks',
            'reach',
            'unique_actions',
            'unique_link_clicks_ctr',
            'clicks',
            'cost_per_unique_action_type',
            'actions',
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
