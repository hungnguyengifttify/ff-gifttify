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
        $title = "AU Dashboard";
        $fromDate = $request->date('fromDate') ? $request->date('fromDate')->format('Y-m-d') : date('Y-m-d');
        $toDate = $request->date('toDate') ? $request->date('toDate')->format('Y-m-d 23:59:59') : date('Y-m-d 23:59:59');
        $labelDate = $request->input('labelDate') ?? 'Today';

        $params = array(
            'fromDate' => new \DateTime($fromDate),
            'toDate' => new \DateTime($toDate),
            'labelDate' => $labelDate,
        );

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('sum(spend) as totalSpend, sum(unique_clicks) as totalUniqueClicks'))
            ->whereIn('account_id', FbAds::$auAccountIds)
            ->where('date_record', '>=', $fromDate)
            ->where('date_record', '<=', $toDate)
            ->first();

        $orders = DB::select("select count(*) as total from shopify_au_thecreattify_orders where CONVERT_TZ(created_at,'UTC','Australia/Sydney') >= :fromDate and CONVERT_TZ(created_at,'UTC','Australia/Sydney') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);
        $totalAmount = DB::select("select sum(total_price)/1.4 as total from shopify_au_thecreattify_orders where CONVERT_TZ(created_at,'UTC','Australia/Sydney') >= :fromDate and CONVERT_TZ(created_at,'UTC','Australia/Sydney') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);

        return view('dashboard', compact('title','totalAmount', 'params', 'orders', 'fbAds'));


        /*$title = "US Dashboard";
        $fromDate = $request->date('fromDate') ? $request->date('fromDate')->format('Y-m-d') : date('Y-m-d');
        $toDate = $request->date('toDate') ? $request->date('toDate')->format('Y-m-d 23:59:59') : date('Y-m-d 23:59:59');
        $labelDate = $request->input('labelDate') ?? 'Today';

        $params = array(
            'fromDate' => new \DateTime($fromDate),
            'toDate' => new \DateTime($toDate),
            'labelDate' => $labelDate,
        );

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('sum(spend) as totalSpend, sum(unique_clicks) as totalUniqueClicks'))
            ->whereIn('account_id', FbAds::$usAccountIds)
            ->where('date_record', '>=', $fromDate)
            ->where('date_record', '<=', $toDate)
            ->first();

        $orders = DB::select("select count(*) as total from shopify_thecreattify_orders where CONVERT_TZ(created_at,'UTC','US/Pacific') >= :fromDate and CONVERT_TZ(created_at,'UTC','US/Pacific') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);
        $totalAmount = DB::select("select sum(total_price) as total from shopify_thecreattify_orders where CONVERT_TZ(created_at,'UTC','US/Pacific') >= :fromDate and CONVERT_TZ(created_at,'UTC','US/Pacific') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);
        return view('dashboard', compact('title','totalAmount', 'params', 'orders', 'fbAds'));
        */
    }
}
