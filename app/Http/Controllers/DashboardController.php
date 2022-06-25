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
        $rangeDates = array_keys($rangeDates);

        $stores = array('us', 'au');
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
            $title = "US Report Detail";
        } elseif ($store == 'au') {
            $title = "AU Report Detail";
        } else {
            dd('Not Allowed');
        }

        $labelDate = $request->input('labelDate') ?? 'Today';
        $range_report = array_search ($labelDate, Dashboard::$rangeDate);

        $dateTimeRange = Dashboard::getDatesByRangeDateLabel($store, $range_report);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $params = array(
            'fromDate' => new \DateTime($fromDate),
            'toDate' => new \DateTime($toDate),
            'labelDate' => $labelDate,
        );

        $accountsAds = Dashboard::getAccountsAdsReportByDate($store, $range_report);
        $countriesAds = Dashboard::getCountryAdsReportByDate($store, $range_report);
        $productTypes = Dashboard::getProductTypesReportByDate($store, $range_report);

        return view('report.dashboard_detail', compact('title', 'store', 'params', 'accountsAds', 'countriesAds', 'productTypes'));
    }
}
