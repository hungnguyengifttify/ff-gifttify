<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Dashboard;

class DashboardController extends Controller {

    public function index(Request $request)
    {
        $rangeDates = Dashboard::$rangeDate;
        unset($rangeDates['custom_range']);
        $rangeDates = array_keys($rangeDates);

        $stores = array('us', 'au', 'singlecloudy');
        $reports = array();
        foreach ($rangeDates as $v) {
            foreach ($stores as $store) {
                $value = Dashboard::getReportByDate($store, $v);
                $reports[$store][] = $value;

                if (!isset($reports['all'][$v])) {
                    $reports['all'][$v]['title'] = $value['title'];
                    $reports['all'][$v]['dateDisplay'] = $value['dateDisplay'];
                    $reports['all'][$v]['fbAds']['totalSpend'] = 0;
                    $reports['all'][$v]['fbAds']['totalUniqueClicks'] = 0;
                    $reports['all'][$v]['orders']['total'] = 0;
                    $reports['all'][$v]['orders']['totalAmount'] = 0;
                    $reports['all'][$v]['productCost'] = 0;
                    $reports['all'][$v]['profitLoss'] = 0;
                    $reports['all'][$v]['mo'] = 0;
                }
                $reports['all'][$v]['fbAds']['totalSpend'] += $value['fbAds']['totalSpend'] ?? 0;
                $reports['all'][$v]['fbAds']['totalUniqueClicks'] += $value['fbAds']['totalUniqueClicks'] ?? 0;
                $reports['all'][$v]['orders']['total'] += $value['orders']['total'] ?? 0;
                $reports['all'][$v]['orders']['totalAmount'] += $value['orders']['totalAmount'] ?? 0;
                $reports['all'][$v]['productCost'] += $value['productCost'] ?? 0;
                $reports['all'][$v]['profitLoss'] += $value['profitLoss'] ?? 0;
                $reports['all'][$v]['mo'] += $value['mo'] ?? 0;
            }
        }

        return view('report.dashboard_sum', array('reports' => $reports));
    }

    public function report_detail(Request $request, $store = 'us') {
        if ($store == 'us') {
            $title = "Thecreattify Report Detail";
        } elseif ($store == 'au') {
            $title = "AU Report Detail";
        } elseif ($store == 'singlecloudy') {
            $title = "SingleCloudy Report Detail";
        } else {
            dd('Not Allowed');
        }

        $debug = $request->input('debug') ?? 0;
        $labelDate = $request->input('labelDate') ?? 'Today';
        $fromDateReq = $request->input('fromDate') ?? '';
        $toDateReq = $request->input('toDate') ?? '';
        $range_report = array_search ($labelDate, Dashboard::$rangeDate);

        $dateTimeRange = Dashboard::getDatesByRangeDateLabel($store, $range_report, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $params = array(
            'fromDate' => new \DateTime($fromDate),
            'toDate' => new \DateTime($toDate),
            'labelDate' => $labelDate,
        );

        $accountsAds = Dashboard::getAccountsAdsReportByDate($store, $range_report, $fromDateReq, $toDateReq);
        $countriesAds = Dashboard::getCountryAdsReportByDate($store, $range_report, $fromDateReq, $toDateReq);
        $productTypes = Dashboard::getProductTypesReportByDate($store, $range_report, $fromDateReq, $toDateReq, $debug);
        $adsTypes = Dashboard::getAdsTypesReportByDate($store, $range_report, $fromDateReq, $toDateReq, $debug);
        $designerAds = Dashboard::getDesignerReportByDate($store, $range_report, $fromDateReq, $toDateReq, $debug);
        $ideaAds = Dashboard::getIdeaReportByDate($store, $range_report, $fromDateReq, $toDateReq, $debug);
        $adsStaffs = Dashboard::getAdsStaffReportByDate($store, $range_report, $fromDateReq, $toDateReq, $debug);

        return view('report.dashboard_detail', compact('title', 'store', 'params', 'accountsAds', 'countriesAds', 'productTypes', 'adsTypes', 'designerAds', 'ideaAds', 'adsStaffs'));
    }

    public function ads_creative(Request $request) {
        $store = $request->input('store') ?? '';
        $code = $request->input('code') ?? '';
        $type = $request->input('type') ?? '';
        $labelDate = $request->input('labelDate') ?? 'Today';
        $fromDateReq = $request->input('fromDate') ?? '';
        $toDateReq = $request->input('toDate') ?? '';

        $range_report = array_search ($labelDate, Dashboard::$rangeDate);
        $dateTimeRange = Dashboard::getDatesByRangeDateLabel($store, $range_report, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $params = array(
            'fromDate' => (new \DateTime($fromDate))->format('d-m-Y'),
            'toDate' => (new \DateTime($toDate))->format('d-m-Y'),
            'labelDate' => $labelDate,
            'store' => $store,
            'code' => $code,
            'type' => $type,
        );

        if (!$store) {
            die('Not Allowed');
        }

        $range_report = array_search ($labelDate, Dashboard::$rangeDate);
        $creatives = Dashboard::getAdsCreativesReportByDate($store, $range_report, $fromDateReq, $toDateReq, $code, $type);

        return view('report.dashboard_ads_creatives', compact('creatives', 'params'));
    }
}
