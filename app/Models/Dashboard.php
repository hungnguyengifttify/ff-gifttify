<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\FbAds;
use Carbon\Carbon;

class Dashboard extends Model
{
    use HasFactory;

    static $rangeDate = array(
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'this_week' => 'This Week',
        'last_week' => 'Last Week',
        'this_month' => 'This Month',
        'custom_range' => 'Custom Range',
    );

    public static function getStoreConfig ($store) {
        if ($store == 'thecreattify') {
            return array (
                'phpTimeZone' => 'America/Los_Angeles',
                'fbAccountIds' => FbAds::$thecreattifyAccountIds,
                'mysqlTimeZone' => 'US/Pacific',
                'radioCurrency' => 1
            );
        } elseif ($store == 'au-thecreattify') {
            return array (
                'phpTimeZone' => 'Australia/Sydney',
                'fbAccountIds' => FbAds::$auThecreattifyAccountIds,
                'mysqlTimeZone' => 'Australia/Sydney',
                'radioCurrency' => 1.4
            );
        } elseif ($store == 'singlecloudy') {
            return array (
                'phpTimeZone' => 'America/Los_Angeles',
                'fbAccountIds' => FbAds::$singlecloudyAccountIds,
                'mysqlTimeZone' => 'US/Pacific',
                'radioCurrency' => 1
            );
        }
        return false;
    }

    public static function getShopifyConfig ($store) {
        if ($store == 'thecreattify') {
            $apiKey = env('SHOPIFY_THECREATTIFY_API_KEY', '');
            $password = env('SHOPIFY_THECREATTIFY_PASSWORD', '');
            $domain = env('SHOPIFY_THECREATTIFY_DOMAIN', '');
            $apiVersion = env('SHOPIFY_THECREATTIFY_API_VERSION', '');

            $dateTimeZone = new \DateTimeZone('America/Los_Angeles');
        } elseif ($store == 'au-thecreattify') {
            $apiKey = env('SHOPIFY_AU_THECREATTIFY_API_KEY', '');
            $password = env('SHOPIFY_AU_THECREATTIFY_PASSWORD', '');
            $domain = env('SHOPIFY_AU_THECREATTIFY_DOMAIN', '');
            $apiVersion = env('SHOPIFY_AU_THECREATTIFY_API_VERSION', '');

            $dateTimeZone = new \DateTimeZone('Australia/Sydney');
        } elseif ($store == 'singlecloudy') {
            $apiKey = env('SHOPIFY_SINGLECLOUDY_API_KEY', '');
            $password = env('SHOPIFY_SINGLECLOUDY_PASSWORD', '');
            $domain = env('SHOPIFY_SINGLECLOUDY_DOMAIN', '');
            $apiVersion = env('SHOPIFY_SINGLECLOUDY_API_VERSION', '');

            $dateTimeZone = new \DateTimeZone('America/Los_Angeles');
        } else {
            return false;
        }

        return array(
            'apiKey' => $apiKey,
            'password' => $password,
            'domain' => $domain,
            'apiVersion' => $apiVersion,
            'dateTimeZone' => $dateTimeZone,
        );
    }

    public static function sort_result($a, $b)
    {
        if ($a['total_order_amount'] == $b['total_order_amount']) {
            return 0;
        }
        return ($a['total_order_amount'] > $b['total_order_amount']) ? -1 : 1;
    }

    public static function sort_result_by_ads_cost($a, $b)
    {
        if ($a['totalSpend'] == $b['totalSpend']) {
            return 0;
        }
        return ($a['totalSpend'] > $b['totalSpend']) ? -1 : 1;
    }

    public static function getReportByDate($store = 'thecreattify', $rangeDate = 'today', $fromDateReq = '', $toDateReq = '') {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];
        $mysqlTimeZone = $storeConfig['mysqlTimeZone'];
        $radioCurrency = $storeConfig['radioCurrency'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('sum(fb_campaign_insights.spend) as totalSpend, sum(fb_campaign_insights.inline_link_clicks) as totalUniqueClicks, sum(fb_campaigns.daily_budget)/100 as dailyBudget'))
            ->leftJoin('fb_campaigns', 'fb_campaign_insights.campaign_id', '=', 'fb_campaigns.fb_campaign_id')
            ->whereIn('fb_campaign_insights.account_id', $fbAccountIds)
            ->where('fb_campaign_insights.date_record', '>=', $fromDate)
            ->where('fb_campaign_insights.date_record', '<=', $toDate)
            ->first();

        $fbAdsets = DB::selectOne("
            select SUM(daily_budget/100) as dailyBudget
            from fb_ad_sets
            where
                daily_budget > 0 and effective_status = 'ACTIVE'
                and campaign_id in (select i.campaign_id from fb_campaign_insights i where i.account_id in (". implode(',', $fbAccountIds) .") and
                i.date_record >= '$fromDate' and i.date_record <= '$toDate');"
        );

        $orders = DB::selectOne("select count(*) as total from orders where store='$store' and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);
        $totalAmount = DB::selectOne("select sum(total_price)/$radioCurrency as total from orders where store='$store' and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);

        return array(
            'title' => self::$rangeDate[$rangeDate] ?? '',
            'dateDisplay' => self::getDateDisplay($rangeDate, $fromDate, $toDate),
            'fbAds' => array (
                'totalSpend' => $fbAds->totalSpend ?? 0,
                'totalUniqueClicks' => $fbAds->totalUniqueClicks ?? 0,
                'dailyBudget' => ($fbAds->dailyBudget ?? 0) + ($fbAdsets->dailyBudget ?? 0),
            ),
            'orders' => array(
                'total' => $orders->total ?? 0,
                'totalAmount' => $totalAmount->total ?? 0,
            ),
            'productCost' => $totalAmount->total * 0.38,
            'profitLoss' => $totalAmount->total - $fbAds->totalSpend - $totalAmount->total * 0.38,
            'mo' => $totalAmount->total > 0 ? 100*($fbAds->totalSpend / $totalAmount->total) : 0,
            'cpc' => $fbAds->totalUniqueClicks != 0 ? $fbAds->totalSpend / $fbAds->totalUniqueClicks : 0,
            'aov' => $orders->total != 0 ? $totalAmount->total / $orders->total : 0,
        );
    }

    public static function getDateDisplay ($rangeDate, $dateTimeStart, $dateTimeEnd) {
        if (in_array($rangeDate, array('today', 'yesterday'))) {
            return Carbon::createFromFormat('Y-m-d', $dateTimeStart)->format('d/m');
        }
        return Carbon::createFromFormat('Y-m-d', $dateTimeStart)->format('d/m')
            . ' to '
            . Carbon::createFromFormat('Y-m-d H:i:s', $dateTimeEnd)->format('d/m');
    }

    public static function getDatesByRangeDateLabel ($store = 'thecreattify', $rangeDate = 'today', $fromDateReq = '', $toDateReq = '') {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $phpTimeZone = $storeConfig['phpTimeZone'];

        Carbon::setWeekStartsAt(Carbon::MONDAY);
        Carbon::setWeekEndsAt(Carbon::SUNDAY);
        $dateTimeStart = Carbon::now($phpTimeZone);
        $dateTimeEnd = Carbon::now($phpTimeZone);

        if ($rangeDate == 'today') {
            $fromDate = $dateTimeStart->format('Y-m-d');
            $toDate = $dateTimeEnd->format('Y-m-d 23:59:59');
        } elseif ($rangeDate == 'yesterday') {
            $dateTimeStart->subDays(1);
            $dateTimeEnd->subDays(1);

            $fromDate = $dateTimeStart->format('Y-m-d');
            $toDate = $dateTimeEnd->format('Y-m-d 23:59:59');
        } elseif ($rangeDate == 'this_week') {
            $fromDate = $dateTimeStart->startOfWeek()->format('Y-m-d');
            $toDate = $dateTimeEnd->endOfWeek()->format('Y-m-d 23:59:59');
        } elseif ($rangeDate == 'last_week') {
            $dateTimeStart->subDays(7);
            $dateTimeEnd->subDays(7);

            $fromDate = $dateTimeStart->startOfWeek()->format('Y-m-d');
            $toDate = $dateTimeEnd->endOfWeek()->format('Y-m-d 23:59:59');
        } elseif ($rangeDate == 'this_month') {
            $fromDate = $dateTimeStart->startOfMonth()->format('Y-m-d');
            $toDate = $dateTimeEnd->endOfMonth()->format('Y-m-d 23:59:59');
        } elseif ($rangeDate == 'custom_range') {
            if (!$fromDateReq || !$toDateReq) {
                $fromDate = $dateTimeStart->format('Y-m-d');
                $toDate = $dateTimeEnd->format('Y-m-d 23:59:59');
            } else {
                $fromDate = Carbon::createFromFormat('Y-m-d', $fromDateReq, $phpTimeZone)->format('Y-m-d');
                $toDate = Carbon::createFromFormat('Y-m-d', $toDateReq, $phpTimeZone)->format('Y-m-d 23:59:59');
            }
        }

        return array (
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        );
    }

    public static function getAccountsAdsReportByDate($store = 'thecreattify', $rangeDate = 'today', $fromDateReq = '', $toDateReq = '') {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('account_name, sum(spend) as totalSpend, sum(inline_link_clicks) as totalUniqueClicks'))
            ->whereIn('account_id', $fbAccountIds)
            ->where('date_record', '>=', $fromDate)
            ->where('date_record', '<=', $toDate)
            ->groupBy('account_name')->get();

        $result = array();
        foreach ($fbAds->all() as $acc) {
            $result[] = array(
                'account_name' => $acc->account_name,
                'totalSpend' => $acc->totalSpend,
                'cpc' => $acc->totalUniqueClicks != 0 ? $acc->totalSpend / $acc->totalUniqueClicks : 0,
            );
        }
        usort($result, [self::class, 'sort_result_by_ads_cost']);

        return $result;

    }

    public static function getCountryAdsReportByDate($store = 'thecreattify', $rangeDate = 'today', $fromDateReq = '', $toDateReq = '') {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];
        $mysqlTimeZone = $storeConfig['mysqlTimeZone'];
        $radioCurrency = $storeConfig['radioCurrency'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $orders = DB::select("select shipping_address->>\"$.country_code\" as country_code,count(*) as total_order, sum(total_price)/$radioCurrency as total_order_amount from orders where store='$store' and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate group by shipping_address->>\"$.country_code\";"
            , ['fromDate' => $fromDate, 'toDate' => $toDate]
        );
        $ordersResult = array();
        foreach ($orders as $o) {
            $o->country_code = $o->country_code ?? 'UNKNOWN';
            $ordersResult[$o->country_code]['total_order'] = $o->total_order;
            $ordersResult[$o->country_code]['total_order_amount'] = $o->total_order_amount;
        }

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('country, sum(spend) as totalSpend, sum(inline_link_clicks) as totalUniqueClicks'))
            ->whereIn('account_id', $fbAccountIds)
            ->where('date_record', '>=', $fromDate)
            ->where('date_record', '<=', $toDate)
            ->groupBy('country')->get();

        $adsResult = array();
        foreach ($fbAds->all() as $acc) {
            $acc->country = $acc->country ?? 'UNKNOWN';
            $adsResult[$acc->country] = array(
                'country' => $acc->country,
                'totalSpend' => $acc->totalSpend,
                'cpc' => $acc->totalUniqueClicks != 0 ? $acc->totalSpend / $acc->totalUniqueClicks : 0,
            );
        }

        $countries = array_merge(array_keys($ordersResult) , array_keys($adsResult));
        $countries = array_unique($countries);
        $result = array();
        foreach ($countries as $country) {
            $result[$country]['country_code'] = $country ?? 'UNKNOWN';
            $result[$country]['total_order'] = $ordersResult[$country]['total_order'] ?? 0;
            $result[$country]['total_order_amount'] = $ordersResult[$country]['total_order_amount'] ?? 0;
            $result[$country]['totalSpend'] = $adsResult[$country]['totalSpend'] ?? 0;
            $result[$country]['cpc'] = $adsResult[$country]['cpc'] ?? 0;
            $result[$country]['mo'] = ($result[$country]['total_order_amount']) > 0 ? 100*($result[$country]['totalSpend'] / $result[$country]['total_order_amount']) : 0;
            $result[$country]['aov'] = $result[$country]['total_order'] != 0 ? $result[$country]['total_order_amount'] / $result[$country]['total_order'] : 0;
        }
        usort($result, [self::class, 'sort_result']);

        return $result;

    }

    public static function getProductTypesReportByDate($store = 'thecreattify', $rangeDate = 'today', $fromDateReq = '', $toDateReq = '',$debug = 0) {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];
        $mysqlTimeZone = $storeConfig['mysqlTimeZone'];
        $radioCurrency = $storeConfig['radioCurrency'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $orders = DB::select("
            select count(Distinct ol.order_id) as total_order, pt.product_type_name, MAX(pt.product_type_code) as product_type_code, sum(ol.price*ol.quantity)/$radioCurrency as total_order_amount, sum(ol.quantity) as total_quantity
            from orders o
            left join order_line_items ol ON o.shopify_id = ol.order_id
            left join products p on ol.product_id = p.shopify_id and p.store = '$store'
            left join shopify_product_type pt on p.product_type = pt.product_type_name
            where o.store = '$store' and p.shopify_id > 0 and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate
            group by p.product_type;
            ;"
            , ['fromDate' => $fromDate, 'toDate' => $toDate]
        );

        $ordersTips = DB::select("
            select count(Distinct ol.order_id) as total_order, 'TIP' as product_type_name, 'TIP' as product_type_code, sum(ol.price*ol.quantity)/$radioCurrency as total_order_amount, sum(ol.quantity) as total_quantity
            from orders o
            left join order_line_items ol ON o.shopify_id = ol.order_id
            where o.store = '$store' and ol.name like 'TIP %' and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate
            ;"
            , ['fromDate' => $fromDate, 'toDate' => $toDate]
        );

        $orders = array_merge($orders, $ordersTips);

        $ordersResult = array();
        foreach ($orders as $o) {
            if ($o->total_order_amount == 0) continue;

            $o->product_type_code = $o->product_type_code ?? 'UNKNOWN';
            if (!isset($ordersResult[$o->product_type_code])) {
                $ordersResult[$o->product_type_code]['product_type_name'] = $o->product_type_name;
                $ordersResult[$o->product_type_code]['total_order_amount'] = 0;
                $ordersResult[$o->product_type_code]['total_quantity'] = 0;
                $ordersResult[$o->product_type_code]['total_order'] = 0;
            }
            $ordersResult[$o->product_type_code]['total_order_amount'] += $o->total_order_amount;
            $ordersResult[$o->product_type_code]['total_quantity'] += $o->total_quantity;
            $ordersResult[$o->product_type_code]['total_order'] += $o->total_order;
        }

        $campaignProductTypeTable = DB::table('campaign_product_type')->get();
        $campaignProductTypeData = $campaignProductTypeTable->keyBy('campaign_name')->all();

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('campaign_name, sum(spend) as totalSpend, sum(inline_link_clicks) as totalUniqueClicks'))
            ->whereIn('account_id', $fbAccountIds)
            ->where('date_record', '>=', $fromDate)
            ->where('date_record', '<=', $toDate)
            ->groupBy('campaign_name')->get();

        $adsResult = array();
        foreach ($fbAds->all() as $v) {
            $v->product_type = $v->campaign_name ? self::getProductTypeFromCampaignName($v->campaign_name, $campaignProductTypeData) : 'UNKNOWN';
            if (!isset($adsResult[$v->product_type])) {
                $adsResult[$v->product_type]['product_type'] = 'UNKNOWN';
                $adsResult[$v->product_type]['totalSpend'] = 0;
                $adsResult[$v->product_type]['totalUniqueClicks'] = 0;
                $adsResult[$v->product_type]['cpc'] = 0;
            }
            $adsResult[$v->product_type]['product_type'] = $v->product_type;
            $adsResult[$v->product_type]['totalSpend'] += $v->totalSpend;
            $adsResult[$v->product_type]['totalUniqueClicks'] += $v->totalUniqueClicks;
            $adsResult[$v->product_type]['cpc'] = ($adsResult[$v->product_type]['totalUniqueClicks'] != 0 ? $adsResult[$v->product_type]['totalSpend'] / $adsResult[$v->product_type]['totalUniqueClicks'] : 0);

            if ($debug == 1 && $adsResult[$v->product_type]['product_type'] == 'UNKNOWN') {
                dump('product_type_UNKNOWN: '. $v->campaign_name);
            }
        }

        $productTypeReports = array_merge(array_keys($ordersResult) , array_keys($adsResult));
        $productTypeReports = array_unique($productTypeReports);

        $productTypeTable = DB::table('product_type')->get();
        $productTypeData = $productTypeTable->keyBy('product_type_code')->all();

        $result = array();
        foreach ($productTypeReports as $v) {
            $result[$v]['product_type_code'] = $v ?: 'UNKNOWN';
            $result[$v]['product_type_name'] = isset($productTypeData[$v]) ? $productTypeData[$v]->product_type_name : '';
            if ($v == 'TIP') {
                $result[$v]['product_type_name'] = 'TIP';
            }

            $result[$v]['total_quantity'] = $ordersResult[$v]['total_quantity'] ?? 0;
            $result[$v]['total_order'] = $ordersResult[$v]['total_order'] ?? 0;
            $result[$v]['total_order_amount'] = $ordersResult[$v]['total_order_amount'] ?? 0;
            $result[$v]['totalSpend'] = $adsResult[$v]['totalSpend'] ?? 0;
            $result[$v]['cpc'] = $adsResult[$v]['cpc'] ?? 0;
            $result[$v]['mo'] = ($result[$v]['total_order_amount']) > 0 ? 100*($result[$v]['totalSpend'] / $result[$v]['total_order_amount']) : 0;
        }
        usort($result, [self::class, 'sort_result']);

        return $result;

    }

    public static function getProductTypeFromCampaignName ($campaignName, $campaignProductTypeData = array()) {
        if (isset($campaignProductTypeData[$campaignName]) && $campaignProductTypeData[$campaignName] != '') {
            return $campaignProductTypeData[$campaignName]->product_type_code;
        }

        $result = array();
        preg_match('/.*Type(\w{0,2})/', $campaignName, $result);
        $productType = $result[1] ?? '';

        if (!$productType) {
            $result = array();
            preg_match('/\w{2}\d{4,5}([A-Z]{2,7})D\d{1,2}/', $campaignName, $result);
            $productType = $result[1] ?? '';
        }

        if (!$productType) {
            $result = array();
            preg_match('/\w{2}\d{4,5}([A-Z]{2,7})D?\d{0,2}/', $campaignName, $result);
            $productType = $result[1] ?? '';
        }
        return $productType ?: 'UNKNOWN';
    }

    public static function getAdsTypesReportByDate ($store = 'thecreattify', $rangeDate = 'today', $fromDateReq = '', $toDateReq = '', $debug = 0) {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];
        $mysqlTimeZone = $storeConfig['mysqlTimeZone'];
        $radioCurrency = $storeConfig['radioCurrency'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('campaign_name, sum(spend) as totalSpend, sum(inline_link_clicks) as totalUniqueClicks'))
            ->whereIn('account_id', $fbAccountIds)
            ->where('date_record', '>=', $fromDate)
            ->where('date_record', '<=', $toDate)
            ->groupBy('campaign_name')->get();

        $adsResult = array(
            'Test' => array(
                'ads_type' => 'Test',
                'totalSpend' => 0,
                'totalCamp' => 0,
                'percent' => 0,
            ),
            'Scale' => array(
                'ads_type' => 'Scale',
                'totalSpend' => 0,
                'totalCamp' => 0,
                'percent' => 0,
            ),
            'Maintain' => array(
                'ads_type' => 'Maintain',
                'totalSpend' => 0,
                'totalCamp' => 0,
                'percent' => 0,
            ),
        );
        $totalSpend = 0;
        foreach ($fbAds->all() as $v) {
            $totalSpend += $v->totalSpend;

            $ads_type = self::getAdsTypeFromCampaignName ($v->campaign_name);
            $adsResult[$ads_type]['ads_type'] = $ads_type;
            $adsResult[$ads_type]['totalSpend'] += $v->totalSpend;
            $adsResult[$ads_type]['totalCamp']++;

            if ($debug == 1 && $adsResult[$ads_type]['ads_type'] == 'UNKNOWN') {
                dump('ads_type_UNKNOWN: '. $v->campaign_name);
            }
        }
        $adsResult['Test']['percent'] = $totalSpend > 0 ? round(100 * $adsResult['Test']['totalSpend'] / $totalSpend, 2) : 0;
        $adsResult['Maintain']['percent'] = $totalSpend > 0 ? round(100 * $adsResult['Maintain']['totalSpend'] / $totalSpend, 2) : 0;
        $adsResult['Scale']['percent'] = ($adsResult['Test']['percent']+$adsResult['Maintain']['percent']) > 0 ? (100 - ($adsResult['Test']['percent']+$adsResult['Maintain']['percent'])) : 0;

        return $adsResult;
    }

    public static function getAdsTypeFromCampaignName ($campaignName) {
        $result = array();
        if (preg_match('/.*test.*/', strtolower($campaignName), $result)) {
            $adsType = 'Test';
        } elseif (preg_match('/.*maintain.*/', strtolower($campaignName), $result)) {
            $adsType = 'Maintain';
        } else {
            $adsType = 'Scale';
        }
        return $adsType;
    }

    public static function getDesignerReportByDate ($store = 'thecreattify', $rangeDate = 'today', $fromDateReq = '', $toDateReq = '', $debug = 0) {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];
        $mysqlTimeZone = $storeConfig['mysqlTimeZone'];
        $radioCurrency = $storeConfig['radioCurrency'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('campaign_name, sum(spend) as totalSpend, sum(inline_link_clicks) as totalUniqueClicks'))
            ->whereIn('account_id', $fbAccountIds)
            ->where('date_record', '>=', $fromDate)
            ->where('date_record', '<=', $toDate)
            ->groupBy('campaign_name')->get();

        $adsResult = array();
        foreach ($fbAds->all() as $v) {
            $designerCode = self::getDesignerFromCampaignName ($v->campaign_name);
            if (!isset($adsResult[$designerCode])) {
                $adsResult[$designerCode]['designerCode'] = $designerCode;
                $adsResult[$designerCode]['totalSpend'] = 0;
                $adsResult[$designerCode]['totalUniqueClicks'] = 0;
            }
            $adsResult[$designerCode]['totalSpend'] += $v->totalSpend;
            $adsResult[$designerCode]['totalUniqueClicks'] += $v->totalUniqueClicks;
            $adsResult[$designerCode]['cpc'] = ($adsResult[$designerCode]['totalUniqueClicks'] != 0 ? $adsResult[$designerCode]['totalSpend'] / $adsResult[$designerCode]['totalUniqueClicks'] : 0);

            if ($debug == 1 && $adsResult[$designerCode]['designerCode'] == 'UNKNOWN') {
                dump('designerCode_UNKNOWN: '. $v->campaign_name);
            }
        }

        $orders = DB::select("
            select count(Distinct ol.order_id) as total_order, sku, sum(ol.price * ol.quantity)/$radioCurrency as total_order_amount, SUM(ol.quantity) as total_quantity
            from orders o
            left join order_line_items ol ON o.shopify_id = ol.order_id
            where o.store = '$store' and ol.product_id > 0 and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate
            group by ol.sku;
            ;"
            , ['fromDate' => $fromDate, 'toDate' => $toDate]
        );

        $ordersResult = array();
        foreach ($orders as $o) {
            $designerCode = self::getDesignerFromSku ($o->sku);
            if (!isset($ordersResult[$designerCode])) {
                $ordersResult[$designerCode]['designerCode'] = $designerCode;
                $ordersResult[$designerCode]['total_order_amount'] = 0;
                $ordersResult[$designerCode]['total_quantity'] = 0;
                $ordersResult[$designerCode]['total_order'] = 0;
            }
            $ordersResult[$designerCode]['designerCode'] = $designerCode;
            $ordersResult[$designerCode]['total_order_amount'] += $o->total_order_amount;
            $ordersResult[$designerCode]['total_quantity'] += $o->total_quantity;
            $ordersResult[$designerCode]['total_order'] += $o->total_order;
        }

        $designerReports = array_merge(array_keys($ordersResult) , array_keys($adsResult));
        $designerReports = array_unique($designerReports);

        $designerTable = DB::table('gifttify_code')->where('type', '=', 'designer')->get();
        $designerData = $designerTable->keyBy('code')->all();

        $result = array();
        foreach ($designerReports as $v) {
            $result[$v]['designer_code'] = $v ?: 'UNKNOWN';
            $result[$v]['designer_name'] = isset($designerData[$v]) ? $designerData[$v]->name : '';

            $result[$v]['total_quantity'] = $ordersResult[$v]['total_quantity'] ?? 0;
            $result[$v]['total_order'] = $ordersResult[$v]['total_order'] ?? 0;
            $result[$v]['total_order_amount'] = $ordersResult[$v]['total_order_amount'] ?? 0;
            $result[$v]['totalSpend'] = $adsResult[$v]['totalSpend'] ?? 0;
            $result[$v]['cpc'] = $adsResult[$v]['cpc'] ?? 0;
            $result[$v]['mo'] = ($result[$v]['total_order_amount']) > 0 ? 100*($result[$v]['totalSpend'] / $result[$v]['total_order_amount']) : 0;
        }
        usort($result, [self::class, 'sort_result']);

        return $result;
    }

    public static function getDesignerFromCampaignName ($campaignName) {
        $result = array();
        preg_match('/\w{2}\d{4,5}[A-Z]{2,7}(D\d{1,2})/', $campaignName, $result);
        $designer = isset($result[1]) ? strtoupper($result[1]) : '';

        if (!$designer) {
            preg_match('/\w{2}\d{4,5}[A-Z]{2,7}(D?\d{0,2})/', $campaignName, $result);
            $designer = isset($result[1]) ? strtoupper($result[1]) : '';
        }

        if ($designer != '' && strpos($designer, 'D') === false) {
            $designer = 'D' . $designer;
        }

        return $designer ?: 'UNKNOWN';
    }

    public static function getDesignerFromSku ($sku) {
        $result = array();
        preg_match('/-(D[0-9PI]{1,2})-/', $sku, $result);
        $designer = isset($result[1]) ? strtoupper($result[1]) : '';

        if (!$designer) {
            preg_match('/\w{2}\d{4,5}[A-Z]{2,7}(D\d{1,2})/', $sku, $result);
            $designer = isset($result[1]) ? strtoupper($result[1]) : '';
        }

        if (!$designer) {
            preg_match('/\w{2}\d{4,5}[A-Z]{2,7}(D?\d{0,2})/', $sku, $result);
            $designer = isset($result[1]) ? strtoupper($result[1]) : '';
        }

        if ($designer != '' && strpos($designer, 'D') === false) {
            $designer = 'D' . $designer;
        }

        return $designer ?: 'UNKNOWN';
    }

    public static function getIdeaReportByDate ($store = 'thecreattify', $rangeDate = 'today', $fromDateReq = '', $toDateReq = '', $debug = 0) {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];
        $mysqlTimeZone = $storeConfig['mysqlTimeZone'];
        $radioCurrency = $storeConfig['radioCurrency'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('campaign_name, sum(spend) as totalSpend, sum(inline_link_clicks) as totalUniqueClicks'))
            ->whereIn('account_id', $fbAccountIds)
            ->where('date_record', '>=', $fromDate)
            ->where('date_record', '<=', $toDate)
            ->groupBy('campaign_name')->get();

        $adsResult = array();
        foreach ($fbAds->all() as $v) {
            $ideaCode = self::getIdeaFromCampaignName ($v->campaign_name);
            if (!isset($adsResult[$ideaCode])) {
                $adsResult[$ideaCode]['idea_code'] = $ideaCode;
                $adsResult[$ideaCode]['totalSpend'] = 0;
                $adsResult[$ideaCode]['totalUniqueClicks'] = 0;
            }
            $adsResult[$ideaCode]['totalSpend'] += $v->totalSpend;
            $adsResult[$ideaCode]['totalUniqueClicks'] += $v->totalUniqueClicks;
            $adsResult[$ideaCode]['cpc'] = ($adsResult[$ideaCode]['totalUniqueClicks'] != 0 ? $adsResult[$ideaCode]['totalSpend'] / $adsResult[$ideaCode]['totalUniqueClicks'] : 0);

            if ($debug == 1 && $adsResult[$ideaCode]['idea_code'] == 'UNKNOWN') {
                dump('idea_code_UNKNOWN: '. $v->campaign_name);
            }
        }

        $orders = DB::select("
            select count(Distinct ol.order_id) as total_order, sku, sum(ol.price * ol.quantity)/$radioCurrency as total_order_amount, sum(ol.quantity) as total_quantity
            from orders o
            left join order_line_items ol ON o.shopify_id = ol.order_id
            where o.store = '$store' and ol.product_id > 0 and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate
            group by ol.sku;
            ;"
            , ['fromDate' => $fromDate, 'toDate' => $toDate]
        );

        $ordersResult = array();
        foreach ($orders as $o) {
            $ideaCode = self::getIdeaFromSku ($o->sku);
            if (!isset($ordersResult[$ideaCode])) {
                $ordersResult[$ideaCode]['idea_code'] = $ideaCode;
                $ordersResult[$ideaCode]['total_order_amount'] = 0;
                $ordersResult[$ideaCode]['total_quantity'] = 0;
                $ordersResult[$ideaCode]['total_order'] = 0;
            }
            $ordersResult[$ideaCode]['idea_code'] = $ideaCode;
            $ordersResult[$ideaCode]['total_order_amount'] += $o->total_order_amount;
            $ordersResult[$ideaCode]['total_quantity'] += $o->total_quantity;
            $ordersResult[$ideaCode]['total_order'] += $o->total_order;
        }

        $ideaReports = array_merge(array_keys($ordersResult) , array_keys($adsResult));
        $ideaReports = array_unique($ideaReports);

        $ideaTable = DB::table('gifttify_code')->where('type', '=', 'idea')->get();
        $ideaData = $ideaTable->keyBy('code')->all();

        $result = array();
        foreach ($ideaReports as $v) {
            $result[$v]['idea_code'] = $v ?: 'UNKNOWN';
            $result[$v]['idea_name'] = isset($ideaData[$v]) ? $ideaData[$v]->name : '';

            $result[$v]['total_order_amount'] = $ordersResult[$v]['total_order_amount'] ?? 0;
            $result[$v]['total_quantity'] = $ordersResult[$v]['total_quantity'] ?? 0;
            $result[$v]['total_order'] = $ordersResult[$v]['total_order'] ?? 0;
            $result[$v]['totalSpend'] = $adsResult[$v]['totalSpend'] ?? 0;
            $result[$v]['cpc'] = $adsResult[$v]['cpc'] ?? 0;
            $result[$v]['mo'] = ($result[$v]['total_order_amount']) > 0 ? 100*($result[$v]['totalSpend'] / $result[$v]['total_order_amount']) : 0;
        }
        usort($result, [self::class, 'sort_result']);

        return $result;
    }

    public static function getIdeaFromCampaignName ($campaignName) {
        $result = array();
        preg_match('/([A-Z]{2})\d{4,5}[A-Z]{2,7}D\d{1,2}/', $campaignName, $result);
        $idea = isset($result[1]) ? strtoupper($result[1]) : '';

        if (!$idea) {
            preg_match('/([A-Z]{2})\d{4,5}[A-Z]{2,7}D?\d{0,2}/', $campaignName, $result);
            $idea = isset($result[1]) ? strtoupper($result[1]) : '';
        }

        return $idea ?: 'UNKNOWN';
    }

    public static function getIdeaFromSku ($sku) {
        $result = array();
        preg_match('/-(i[0-9]{2,3})-/', $sku, $result);
        $idea = isset($result[1]) ? $result[1] : '';
        if ($idea == 'i101') {
            $idea = 'DU';
        } elseif ($idea == 'i102') {
            $idea = 'TR';
        } elseif ($idea == 'i103') {
            $idea = 'HU';
        } elseif ($idea == 'i104') {
            $idea = 'LE';
        } elseif ($idea == 'i36') {
            $idea = 'NG';
        } elseif ($idea == 'a101') {
            $idea = 'VA';
        }

        if (!$idea) {
            preg_match('/^([A-Z]{2})\d{4,5}[A-Z]{2,7}D\d{1,2}/', $sku, $result);
            $idea = isset($result[1]) ? strtoupper($result[1]) : '';
        }

        if (!$idea) {
            preg_match('/([A-Z]{2})\d{4,5}[A-Z]{2,7}D?\d{0,2}/', $sku, $result);
            $idea = isset($result[1]) ? strtoupper($result[1]) : '';
        }

        return $idea ?: 'UNKNOWN';
    }

    public static function getAdsStaffReportByDate ($store = 'thecreattify', $rangeDate = 'today', $fromDateReq = '', $toDateReq = '', $debug = 0) {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('campaign_name, sum(spend) as totalSpend, sum(inline_link_clicks) as totalUniqueClicks'))
            ->whereIn('account_id', $fbAccountIds)
            ->where('date_record', '>=', $fromDate)
            ->where('date_record', '<=', $toDate)
            ->groupBy('campaign_name')->get();

        $adsResult = array();
        foreach ($fbAds->all() as $v) {
            $adsStaff = self::getAdsStaffFromCampaignName ($v->campaign_name);
            if (!isset($adsResult[$adsStaff])) {
                $adsResult[$adsStaff]['adsStaff'] = $adsStaff;
                $adsResult[$adsStaff]['totalSpend'] = 0;
                $adsResult[$adsStaff]['totalCamp'] = 0;
            }
            $adsResult[$adsStaff]['adsStaff'] = $adsStaff;
            $adsResult[$adsStaff]['totalSpend'] += $v->totalSpend;
            $adsResult[$adsStaff]['totalCamp']++;

            if ($debug == 1 && $adsStaff == 'UNKNOWN') {
                dump('adsStaff_UNKNOWN: '. $v->campaign_name);
            }
        }

        return $adsResult;
    }

    public static function getAdsStaffFromCampaignName ($campaignName) {
        $result = array();
        $adsType = "UNKNOWN";
        if (preg_match('/.*phong.*/', strtolower($campaignName), $result)) {
            $adsType = 'Phong';
        } elseif (preg_match('/.*VA.*/', $campaignName, $result)) {
            $adsType = 'Việt Anh';
        } elseif (preg_match('/.*hoang.*/', strtolower($campaignName), $result)) {
            $adsType = 'Hoàng';
        } elseif (preg_match('/^m.*/', strtolower($campaignName), $result)) {
            $adsType = 'Minh';
        }
        return $adsType;
    }

    public static function getAdsCreativesReportByDate ($store = 'thecreattify', $rangeDate = 'today', $fromDateReq = '', $toDateReq = '', $code = '', $type = '') {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $fbAds = DB::table('fb_ads_insights')
            ->select(DB::raw('campaign_name, max(fb_ads_creatives.id) as creative_id, max(fb_ads_insights.campaign_id) as campaign_id, ad_id, SUM(spend) as totalSpend, sum(inline_link_clicks) as totalUniqueClicks, max(body) as body, max(effective_object_story_id) as effective_object_story_id, max(image_url) as image_url, max(fb_ads_creatives.title) as title, max(fb_ads_creatives.status) as status, max(title) as title, max(object_story_spec) as object_story_spec'))
            ->leftJoin('fb_ads', 'fb_ads_insights.ad_id', '=', 'fb_ads.id')
            ->leftJoin('fb_ads_creatives', 'fb_ads.creative_id', '=', 'fb_ads_creatives.id')
            ->whereIn('fb_ads_insights.account_id', $fbAccountIds)
            ->where('date_record', '>=', $fromDate)
            ->where('date_record', '<=', $toDate)
            ->groupBy(array('ad_id', 'campaign_name'))->get();

        $adsResult = array();
        foreach ($fbAds->all() as $v) {
            $checkCode = '';
            if ($type == 'idea') {
                $checkCode = self::getIdeaFromCampaignName ($v->campaign_name);
            } elseif ($type == 'designer') {
                $checkCode = self::getDesignerFromCampaignName ($v->campaign_name);
            } elseif ($type == 'product_type') {
                $checkCode = self::getProductTypeFromCampaignName ($v->campaign_name);
            }

            if ($checkCode != $code) continue;

            $campaignId = $v->campaign_id;
            $linkId = $v->effective_object_story_id;

            if (!isset($adsResult[$linkId]['campaigns'][$campaignId])) {
                $adsResult[$linkId]['campaigns'][$campaignId]['campaignName'] = $v->campaign_name;
                $adsResult[$linkId]['campaigns'][$campaignId]['idea_code'] = $code;
                $adsResult[$linkId]['campaigns'][$campaignId]['totalSpend'] = 0;
                $adsResult[$linkId]['campaigns'][$campaignId]['totalUniqueClicks'] = 0;
            }
            $adsResult[$linkId]['campaigns'][$campaignId]['totalSpend'] += $v->totalSpend;
            $adsResult[$linkId]['campaigns'][$campaignId]['totalUniqueClicks'] += $v->totalUniqueClicks;
            $adsResult[$linkId]['campaigns'][$campaignId]['cpc'] = ($adsResult[$linkId]['campaigns'][$campaignId]['totalUniqueClicks'] != 0 ? $adsResult[$linkId]['campaigns'][$campaignId]['totalSpend'] / $adsResult[$linkId]['campaigns'][$campaignId]['totalUniqueClicks'] : 0);

            if (!isset($adsResult[$linkId]['body'])) {
                $adsResult[$linkId]['body'] = $v->body;
                $adsResult[$linkId]['ads_url'] = isset($linkId) ? 'https://facebook.com/' . $linkId : '';
                $adsResult[$linkId]['image_url'] = $v->image_url ?: '';
                $adsResult[$linkId]['title'] = $v->title ?: '';
                $adsResult[$linkId]['status'] = $v->status ?: '';
                $adsResult[$linkId]['object_story_spec'] = $v->object_story_spec ?: '';
                $adsResult[$linkId]['countCampaign'] = 0;
                $adsResult[$linkId]['totalSpendCampaign'] = 0;
                $adsResult[$linkId]['totalUniqueClicksCampaign'] = 0;
            }
            $adsResult[$linkId]['countCampaign']++;
            $adsResult[$linkId]['totalSpendCampaign'] += $v->totalSpend;
            $adsResult[$linkId]['totalUniqueClicksCampaign'] += $v->totalUniqueClicks;
            $adsResult[$linkId]['cpc'] = $adsResult[$linkId]['totalUniqueClicksCampaign'] != 0 ? $adsResult[$linkId]['totalSpendCampaign'] / $adsResult[$linkId]['totalUniqueClicksCampaign'] : 0;

        }
        return $adsResult;
    }

}
