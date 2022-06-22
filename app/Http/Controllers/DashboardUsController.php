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
        $dateTime = new \DateTime("now", new \DateTimeZone('America/Los_Angeles'));
        $fromDate = $request->date('fromDate') ? $request->date('fromDate')->format('Y-m-d') : $dateTime->format('Y-m-d');
        $toDate = $request->date('toDate') ? $request->date('toDate')->format('Y-m-d 23:59:59') : $dateTime->format('Y-m-d 23:59:59');
        $labelDate = $request->input('labelDate') ?? 'Today';

        $params = array(
            'fromDate' => new \DateTime($fromDate),
            'toDate' => new \DateTime($toDate),
            'labelDate' => $labelDate,
        );

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('sum(spend) as totalSpend, sum(inline_link_clicks) as totalUniqueClicks'))
            ->whereIn('account_id', FbAds::$usAccountIds)
            ->where('date_record', '>=', $fromDate)
            ->where('date_record', '<=', $toDate)
            ->first();

        $orders = DB::select("select count(*) as total from orders where store='us' and CONVERT_TZ(shopify_created_at,'UTC','US/Pacific') >= :fromDate and CONVERT_TZ(shopify_created_at,'UTC','US/Pacific') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);
        $totalAmount = DB::select("select sum(total_price) as total from orders where store='us' and CONVERT_TZ(shopify_created_at,'UTC','US/Pacific') >= :fromDate and CONVERT_TZ(shopify_created_at,'UTC','US/Pacific') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);
        return view('dashboard', compact('title','totalAmount', 'params', 'orders', 'fbAds'));
    }
}
