<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FbAds;

class DashboardUsController extends Controller {

    public function index(Request $request)
    {
        $title = "US Dashboard";
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

        $orders = DB::select("select count(*) as total from shopify_thecreattify_orders where CONVERT_TZ(created_at,'UTC','US/Pacific') >= :fromDate and CONVERT_TZ(created_at,'UTC','US/Pacific') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);
        $totalAmount = DB::select("select sum(total_price) as total from shopify_thecreattify_orders where CONVERT_TZ(created_at,'UTC','US/Pacific') >= :fromDate and CONVERT_TZ(created_at,'UTC','US/Pacific') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);
        return view('dashboard', compact('title','totalAmount', 'params', 'orders', 'fbAds'));
    }
}
