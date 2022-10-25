<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use FKRediSearch\Query\Query;
use FKRediSearch\Query\QueryBuilder;
use FKRediSearch\Setup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Dashboard;
use App\Services\GoogleAnalytics;
use App\Models\GaCampaignReports;
use Illuminate\Support\Facades\Redis;


class DashboardTestController extends Controller {
    public function index(Request $request)
    {
        $rangeDates = Dashboard::$rangeDate;
        unset($rangeDates['custom_range']);
        $rangeDates = array_keys($rangeDates);

        $stores = Dashboard::getStoresList();
        $storesConfig = Dashboard::getAllStoreConfig();
        $reports = array();
        foreach ($rangeDates as $v) {
            foreach ($stores as $store) {
                $value = Dashboard::getReportByDate($store, $v, '', '', true);
                $reports[$store][] = $value;

                if (!isset($reports['all'][$v])) {
                    $reports['all'][$v]['title'] = $value['title'];
                    $reports['all'][$v]['dateDisplay'] = $value['dateDisplay'];
                    $reports['all'][$v]['fbAds']['totalSpend'] = 0;
                    $reports['all'][$v]['ggAds']['ga_ad_cost'] = 0;
                    $reports['all'][$v]['fbAds']['totalUniqueClicks'] = 0;
                    $reports['all'][$v]['orders']['total'] = 0;
                    $reports['all'][$v]['orders']['totalAmount'] = 0;
                    $reports['all'][$v]['productCost'] = 0;
                    $reports['all'][$v]['profitLoss'] = 0;
                    $reports['all'][$v]['mo'] = 0;
                }
                $reports['all'][$v]['ggAds']['ga_ad_cost'] += $value['ggAds']['ga_ad_cost'] ?? 0;
                $reports['all'][$v]['fbAds']['totalSpend'] += $value['fbAds']['totalSpend'] ?? 0;
                $reports['all'][$v]['fbAds']['totalUniqueClicks'] += $value['fbAds']['totalUniqueClicks'] ?? 0;
                $reports['all'][$v]['orders']['total'] += $value['orders']['total'] ?? 0;
                $reports['all'][$v]['orders']['totalAmount'] += $value['orders']['totalAmount'] ?? 0;
                $reports['all'][$v]['productCost'] += $value['productCost'] ?? 0;
                $reports['all'][$v]['profitLoss'] += $value['profitLoss'] ?? 0;
                $reports['all'][$v]['mo'] += $value['mo'] ?? 0;
            }
        }

        return view('report.dashboard_sum', array('reports' => $reports, 'storesConfig' => $storesConfig));
    }
}
