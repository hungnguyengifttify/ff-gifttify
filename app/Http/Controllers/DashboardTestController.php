<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FbAds;
use App\Models\Dashboard;

use App\Services\GoogleDrive;

class DashboardTestController extends Controller {

    public function index(Request $request)
    {
        $store = $request->input('store') ?? '';
        $labelDate = $request->input('labelDate') ?? 'Today';
        $fromDateReq = $request->input('fromDate') ?? '';
        $toDateReq = $request->input('toDate') ?? '';
        $debug = $request->input('debug') ?? 0;

        $range_report = array_search ($labelDate, Dashboard::$rangeDate);
        $dateTimeRange = Dashboard::getDatesByRangeDateLabel($store, $range_report, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $params = array(
            'fromDate' => (new \DateTime($fromDate)),
            'toDate' => (new \DateTime($toDate)),
            'labelDate' => $labelDate,
            'store' => $store,
        );

        if (!$store) {
            die('Not Allowed');
        }

        $range_report = array_search ($labelDate, Dashboard::$rangeDate);
        $campaigns = Dashboard::getCampaignInfoByDate($store, $range_report, $fromDateReq, $toDateReq, $debug);
        return view('report.dashboard_detail_test', compact('campaigns', 'params', 'store'));

    }
}
