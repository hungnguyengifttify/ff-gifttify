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
    );

    public static function getStoreConfig ($store) {
        if ($store == 'us') {
            return array (
                'phpTimeZone' => 'America/Los_Angeles',
                'fbAccountIds' => FbAds::$usAccountIds,
                'mysqlTimeZone' => 'US/Pacific',
                'radioCurrency' => 1
            );
        } elseif ($store == 'au') {
            return array (
                'phpTimeZone' => 'Australia/Sydney',
                'fbAccountIds' => FbAds::$auAccountIds,
                'mysqlTimeZone' => 'Australia/Sydney',
                'radioCurrency' => 1.4
            );
        }
        return false;
    }

    public static function getReportByDate($store = 'us', $rangeDate = 'today') {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];
        $mysqlTimeZone = $storeConfig['mysqlTimeZone'];
        $radioCurrency = $storeConfig['radioCurrency'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('sum(spend) as totalSpend, sum(inline_link_clicks) as totalUniqueClicks'))
            ->whereIn('account_id', $fbAccountIds)
            ->where('date_record', '>=', $fromDate)
            ->where('date_record', '<=', $toDate)
            ->first();

        $orders = DB::selectOne("select count(*) as total from orders where store='$store' and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);
        $totalAmount = DB::selectOne("select sum(total_price)/$radioCurrency as total from orders where store='$store' and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);

        return array(
            'title' => self::$rangeDate[$rangeDate] ?? '',
            'dateDisplay' => self::getDateDisplay($rangeDate, $fromDate, $toDate),
            'fbAds' => (array)$fbAds,
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

    public static function getDatesByRangeDateLabel ($store = 'us', $rangeDate = 'today') {
        if ($store == 'us') {
            $phpTimeZone = 'America/Los_Angeles';
        } elseif ($store == 'au') {
            $phpTimeZone = 'Australia/Sydney';
        }

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
        }
        return array (
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        );
    }

    public static function getAccountsAdsReportByDate($store = 'us', $rangeDate = 'today') {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate);
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

        return $result;

    }

    public static function getCountryAdsReportByDate($store = 'us', $rangeDate = 'today') {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];
        $mysqlTimeZone = $storeConfig['mysqlTimeZone'];
        $radioCurrency = $storeConfig['radioCurrency'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate);
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
        }

        return $result;

    }

    public static function getProductTypesReportByDate($store = 'us', $rangeDate = 'today') {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];
        $mysqlTimeZone = $storeConfig['mysqlTimeZone'];
        $radioCurrency = $storeConfig['radioCurrency'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $orders = DB::select("
            select pt.product_type_name, MAX(pt.product_type_code) as product_type_code , MAX(pt.product_type_code) as product_type_code, sum(ol.price)/$radioCurrency as total_order_amount
            from orders o
            left join order_line_items ol ON o.shopify_id = ol.order_id
            left join products p on ol.product_id = p.shopify_id and p.store = '$store'
            left join shopify_product_type pt on p.product_type = pt.product_type_name
            where o.store = '$store' and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate
            group by p.product_type;
            ;"
            , ['fromDate' => $fromDate, 'toDate' => $toDate]
        );

        $ordersResult = array();
        foreach ($orders as $o) {
            $o->product_type_code = $o->product_type_code ?? 'UNKNOWN';
            $ordersResult[$o->product_type_code]['product_type_name'] = $o->product_type_name;
            $ordersResult[$o->product_type_code]['total_order_amount'] = $o->total_order_amount;
        }

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('campaign_name, sum(spend) as totalSpend, sum(inline_link_clicks) as totalUniqueClicks'))
            ->whereIn('account_id', $fbAccountIds)
            ->where('date_record', '>=', $fromDate)
            ->where('date_record', '<=', $toDate)
            ->groupBy('campaign_name')->get();

        $adsResult = array();
        foreach ($fbAds->all() as $v) {
            $v->product_type = $v->campaign_name ? self::getProductTypeFromCampaignName($v->campaign_name) : 'UNKNOWN';
            if (!isset($adsResult[$v->product_type])) {
                $adsResult[$v->product_type]['product_type'] = 'UNKNOWN';
                $adsResult[$v->product_type]['totalSpend'] = 0;
                $adsResult[$v->product_type]['cpc'] = 0;
            }
            $adsResult[$v->product_type]['product_type'] = $v->product_type;
            $adsResult[$v->product_type]['totalSpend'] += $v->totalSpend;
            $adsResult[$v->product_type]['cpc'] += ($v->totalUniqueClicks != 0 ? $v->totalSpend / $v->totalUniqueClicks : 0);
        }

        $productTypeReports = array_merge(array_keys($ordersResult) , array_keys($adsResult));
        $productTypeReports = array_unique($productTypeReports);

        $productTypeTable = DB::table('product_type')->get();
        $productTypeData = $productTypeTable->keyBy('product_type_code')->all();

        $result = array();
        foreach ($productTypeReports as $v) {
            $result[$v]['product_type_code'] = $v ?: 'UNKNOWN';
            $result[$v]['product_type_name'] = isset($productTypeData[$v]) ? $productTypeData[$v]->product_type_name : '';

            $result[$v]['total_order_amount'] = $ordersResult[$v]['total_order_amount'] ?? 0;
            $result[$v]['totalSpend'] = $adsResult[$v]['totalSpend'] ?? 0;
            $result[$v]['cpc'] = $adsResult[$v]['cpc'] ?? 0;
            $result[$v]['mo'] = ($result[$v]['total_order_amount']) > 0 ? 100*($result[$v]['totalSpend'] / $result[$v]['total_order_amount']) : 0;
        }

        return $result;

    }

    public static function getProductTypeFromCampaignName ($campaignName) {
        $result = array();
        preg_match('/.*Type(\w+)/', $campaignName, $result);

        $productType = $result[1] ?? '';
        if (!$productType) {
            $result = array();
            preg_match('/\w{2}\d{4,5}(.+)D\w{1,2}/', $campaignName, $result);
            $productType = $result[1] ?? '';
        }
        return $productType ?: 'UNKNOWN';
    }
}
