<?php

namespace App\Models;

use App\Models\FbAds;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\FbAccount;
use App\Models\RedisGtf;
use Carbon\Carbon;

class Dashboard extends Model
{
    use HasFactory;

    static $rangeDate = array(
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'last_7_days' => 'Last 7 Days',
        'this_week' => 'This Week',
        'last_week' => 'Last Week',
        'this_month' => 'This Month',
        'custom_range' => 'Custom Range',
    );

    public static function getAllStoreConfig () {
        return array(
            'lumelightart' => array (
                'storeType' => array('shopify'),
                'domain' => 'lumelightart.co',
                'common' => array (
                    'phpTimeZone' => 'America/Los_Angeles',
                    'fbAccountIds' => FbAds::$lumelightartAccountIds,
                    'mysqlTimeZone' => 'US/Pacific',
                    'radioCurrency' => 1
                ),
                'shopify' => array (
                    'apiKey' => env('SHOPIFY_LUMELIGHTART_API_KEY', ''),
                    'password' => env('SHOPIFY_LUMELIGHTART_PASSWORD', ''),
                    'domain' => env('SHOPIFY_LUMELIGHTART_DOMAIN', ''),
                    'apiVersion' => env('SHOPIFY_LUMELIGHTART_API_VERSION', ''),
                    'dateTimeZone' => new \DateTimeZone('America/Los_Angeles'),
                ),
                'google' => array (
                    'viewId' => ''
                )
            ),
            'ip' => array (
                'storeType' => array('shopify'),
                'domain' => 'impossiblepuzzle.co',
                'common' => array (
                    'phpTimeZone' => 'America/Los_Angeles',
                    'fbAccountIds' => FbAds::$ipAccountIds,
                    'mysqlTimeZone' => 'US/Pacific',
                    'radioCurrency' => 1
                ),
                'shopify' => array (
                    'apiKey' => env('SHOPIFY_IP_API_KEY', ''),
                    'password' => env('SHOPIFY_IP_PASSWORD', ''),
                    'domain' => env('SHOPIFY_IP_DOMAIN', ''),
                    'apiVersion' => env('SHOPIFY_IP_API_VERSION', ''),
                    'dateTimeZone' => new \DateTimeZone('America/Los_Angeles'),
                ),
                'google' => array (
                    'viewId' => ''
                )
            ),
            'thecreattify' => array (
                'storeType' => array('gtf', 'shopify'),
                'domain' => 'thecreattify.com',
                'common' => array (
                    'phpTimeZone' => 'America/Los_Angeles',
                    'fbAccountIds' => FbAds::$thecreattifyAccountIds,
                    'mysqlTimeZone' => 'US/Pacific',
                    'radioCurrency' => 1
                ),
                'shopify' => array (
                    'apiKey' => env('SHOPIFY_THECREATTIFY_API_KEY', ''),
                    'password' => env('SHOPIFY_THECREATTIFY_PASSWORD', ''),
                    'domain' => env('SHOPIFY_THECREATTIFY_DOMAIN', ''),
                    'apiVersion' => env('SHOPIFY_THECREATTIFY_API_VERSION', ''),
                    'dateTimeZone' => new \DateTimeZone('America/Los_Angeles'),
                ),
                'google' => array (
                    'viewId' => '256866587'
                )
            ),

            'store-gifttify' => array (
                'storeType' => array('gtf'),
                'domain' => 'store.gifttify.com',
                'common' => array (
                    'phpTimeZone' => 'America/Los_Angeles',
                    'fbAccountIds' => FbAds::$storeGifttifyAccountIds,
                    'mysqlTimeZone' => 'US/Pacific',
                    'radioCurrency' => 1
                ),
                'shopify' => array (
                    'apiKey' => env('SHOPIFY_STORE_GIFTTIFY_API_KEY', ''),
                    'password' => env('SHOPIFY_STORE_GIFTTIFY_PASSWORD', ''),
                    'domain' => env('SHOPIFY_STORE_GIFTTIFY_DOMAIN', ''),
                    'apiVersion' => env('SHOPIFY_STORE_GIFTTIFY_API_VERSION', ''),
                    'dateTimeZone' => new \DateTimeZone('America/Los_Angeles'),
                ),
                'google' => array (
                    'viewId' => '278096736'
                )
            ),

            /*'au-thecreattify' => array (
                'storeType' => array('shopify'),
                'domain' => 'au.thecreattify.com',
                'common' => array (
                    'phpTimeZone' => 'Australia/Sydney',
                    'fbAccountIds' => FbAds::$auThecreattifyAccountIds,
                    'mysqlTimeZone' => 'Australia/Sydney',
                    'radioCurrency' => 1.4
                ),
                'shopify' => array (
                    'apiKey' => env('SHOPIFY_AU_THECREATTIFY_API_KEY', ''),
                    'password' => env('SHOPIFY_AU_THECREATTIFY_PASSWORD', ''),
                    'domain' => env('SHOPIFY_AU_THECREATTIFY_DOMAIN', ''),
                    'apiVersion' => env('SHOPIFY_AU_THECREATTIFY_API_VERSION', ''),
                    'dateTimeZone' => new \DateTimeZone('Australia/Sydney'),
                ),
                'google' => array (
                    'viewId' => '269048495'
                )
            ),*/

            /*'singlecloudy' => array (
                'storeType' => array('shopify'),
                'domain' => 'singlecloudy.com',
                'common' => array (
                    'phpTimeZone' => 'America/Los_Angeles',
                    'fbAccountIds' => FbAds::$singlecloudyAccountIds,
                    'mysqlTimeZone' => 'US/Pacific',
                    'radioCurrency' => 1
                ),
                'shopify' => array (
                    'apiKey' => env('SHOPIFY_SINGLECLOUDY_API_KEY', ''),
                    'password' => env('SHOPIFY_SINGLECLOUDY_PASSWORD', ''),
                    'domain' => env('SHOPIFY_SINGLECLOUDY_DOMAIN', ''),
                    'apiVersion' => env('SHOPIFY_SINGLECLOUDY_API_VERSION', ''),
                    'dateTimeZone' => new \DateTimeZone('America/Los_Angeles'),
                ),
                'google' => array (
                    'viewId' => '272196026'
                )
            ),*/

            'gifttifyus' => array (
                'storeType' => array('shopify'),
                'domain' => 'gifttifyus.com',
                'common' => array (
                    'phpTimeZone' => 'America/Los_Angeles',
                    'fbAccountIds' => FbAds::$gifttifyusAccountIds,
                    'mysqlTimeZone' => 'US/Pacific',
                    'radioCurrency' => 1
                ),
                'shopify' => array (
                    'apiKey' => env('SHOPIFY_GIFTTIFYUS_API_KEY', ''),
                    'password' => env('SHOPIFY_GIFTTIFYUS_PASSWORD', ''),
                    'domain' => env('SHOPIFY_GIFTTIFYUS_DOMAIN', ''),
                    'apiVersion' => env('SHOPIFY_GIFTTIFYUS_API_VERSION', ''),
                    'dateTimeZone' => new \DateTimeZone('America/Los_Angeles'),
                ),
                'google' => array (
                    'viewId' => '253293522'
                )
            ),

            'owllify' => array (
                'storeType' => array('gtf', 'shopify'),
                'domain' => 'owllify.com',
                'common' => array (
                    'phpTimeZone' => 'America/Los_Angeles',
                    'fbAccountIds' => FbAds::$owllifyAccountIds,
                    'mysqlTimeZone' => 'US/Pacific',
                    'radioCurrency' => 1
                ),
                'shopify' => array (
                    'apiKey' => env('SHOPIFY_OWLLIFY_API_KEY', ''),
                    'password' => env('SHOPIFY_OWLLIFY_PASSWORD', ''),
                    'domain' => env('SHOPIFY_OWLLIFY_DOMAIN', ''),
                    'apiVersion' => env('SHOPIFY_OWLLIFY_API_VERSION', ''),
                    'dateTimeZone' => new \DateTimeZone('America/Los_Angeles'),
                ),
                'google' => array (
                    'viewId' => '244142345'
                )
            ),

            'hippiesy' => array (
                'storeType' => array('shopify'),
                'domain' => 'hippiesy.com',
                'common' => array (
                    'phpTimeZone' => 'America/Los_Angeles',
                    'fbAccountIds' => FbAds::$hippiesyAccountIds,
                    'mysqlTimeZone' => 'US/Pacific',
                    'radioCurrency' => 1
                ),
                'shopify' => array (
                    'apiKey' => env('SHOPIFY_HIPPIESY_API_KEY', ''),
                    'password' => env('SHOPIFY_HIPPIESY_PASSWORD', ''),
                    'domain' => env('SHOPIFY_HIPPIESY_DOMAIN', ''),
                    'apiVersion' => env('SHOPIFY_HIPPIESY_API_VERSION', ''),
                    'dateTimeZone' => new \DateTimeZone('America/Los_Angeles'),
                ),
                'google' => array (
                    'viewId' => '272705190'
                )
            ),
/*
            'getcus' => array (
                'storeType' => array('shopify'),
                'domain' => 'getcus.gifttify.com',
                'common' => array (
                    'phpTimeZone' => 'America/Los_Angeles',
                    'fbAccountIds' => FbAds::$getcusAccountIds,
                    'mysqlTimeZone' => 'US/Pacific',
                    'radioCurrency' => 1
                ),
                'shopify' => array (
                    'apiKey' => env('SHOPIFY_GETCUS_API_KEY', ''),
                    'password' => env('SHOPIFY_GETCUS_PASSWORD', ''),
                    'domain' => env('SHOPIFY_GETCUS_DOMAIN', ''),
                    'apiVersion' => env('SHOPIFY_GETCUS_API_VERSION', ''),
                    'dateTimeZone' => new \DateTimeZone('America/Los_Angeles'),
                ),
                'google' => array (
                    'viewId' => '275497496'
                )
            ),
*/
            'whelands' => array (
                'storeType' => array('gtf'),
                'domain' => 'whelands.com',
                'common' => array (
                    'phpTimeZone' => 'Australia/Sydney',
                    'fbAccountIds' => FbAds::$whelandsAccountIds,
                    'mysqlTimeZone' => 'Australia/Sydney',
                    'radioCurrency' => 1
                ),
                'shopify' => array (
                    'apiKey' => env('SHOPIFY_WHELANDS_API_KEY', ''),
                    'password' => env('SHOPIFY_WHELANDS_PASSWORD', ''),
                    'domain' => env('SHOPIFY_WHELANDS_DOMAIN', ''),
                    'apiVersion' => env('SHOPIFY_WHELANDS_API_VERSION', ''),
                    'dateTimeZone' => new \DateTimeZone('Australia/Sydney'),
                ),
                'google' => array (
                    'viewId' => ''
                )
            ),
        );

    }

    public static function getStoresList () {
        $allStore = Dashboard::getAllStoreConfig();
        return array_keys($allStore);
    }

    public static function getShopifyStoresList () {
        $allStore = Dashboard::getAllStoreConfig();
        $shopifyStoreList = array();
        foreach ($allStore as $store => $value) {
            if (in_array('shopify', $value['storeType'])) {
                $shopifyStoreList[] = $store;
            }
        }
        return $shopifyStoreList;
    }

    public static function getStoreFromAccountId ($accountId) {
        $allStore = Dashboard::getAllStoreConfig();
        foreach ($allStore as $store => $value) {
            if ( in_array($accountId, $value['common']['fbAccountIds']) ) {
                return $store;
            }
        }
        return '';
    }

    public static function getStoreConfig ($store) {
        $allStore = Dashboard::getAllStoreConfig();
        return isset($allStore[$store]['common']) ? $allStore[$store]['common'] : false;
    }

    public static function getStoreType ($store) {
        $allStore = Dashboard::getAllStoreConfig();
        return isset($allStore[$store]['storeType']) ? $allStore[$store]['storeType'] : false;
    }

    public static function getShopifyConfig ($store) {
        $allStore = Dashboard::getAllStoreConfig();
        return isset($allStore[$store]['shopify']) ? $allStore[$store]['shopify'] : false;
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

    public static function getReportByDate($store = 'thecreattify', $rangeDate = 'today', $fromDateReq = '', $toDateReq = '', $debug = false) {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];
        $mysqlTimeZone = $storeConfig['mysqlTimeZone'];
        $radioCurrency = $storeConfig['radioCurrency'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('sum(fb_campaign_insights.spend) as totalSpend,
                sum(fb_campaign_insights.inline_link_clicks) as totalUniqueClicks,
                sum(fb_campaigns.daily_budget)/100 as dailyBudget'))
            ->leftJoin('fb_campaigns', 'fb_campaign_insights.campaign_id', '=', 'fb_campaigns.fb_campaign_id')
            ->whereIn('fb_campaign_insights.account_id', $fbAccountIds)
            ->where('fb_campaign_insights.date_record', '>=', $fromDate)
            ->where('fb_campaign_insights.date_record', '<=', $toDate)
            ->first();

        $gaAds = DB::table('ga_campaign_reports')
            ->select(DB::raw('SUM(transactions) as ga_total_order,
                SUM(transaction_revenue) as ga_total_order_amount,
                SUM(ad_cost) as ga_ad_cost'))
            ->where('ga_campaign_reports.store', $store)
            ->where('ga_campaign_reports.date_record', '>=', $fromDate)
            ->where('ga_campaign_reports.date_record', '<=', $toDate)
            ->where('ga_campaign_reports.transactions', '>', 0)
            ->first();

        if ($fbAccountIds) {
            $fbAdsets = DB::selectOne("
            select SUM(daily_budget/100) as dailyBudget
            from fb_ad_sets
            where
                daily_budget > 0 and effective_status = 'ACTIVE'
                and campaign_id in (select i.campaign_id from fb_campaign_insights i where i.account_id in (". implode(',', $fbAccountIds) .") and
                i.date_record >= '$fromDate' and i.date_record <= '$toDate');"
            );
        } else {
            $fbAdsets = array();
        }


        $orders = DB::selectOne("select count(*) as total from orders where store='$store' and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);
        $totalAmount = DB::selectOne("select sum(total_price)/$radioCurrency as total from orders where store='$store' and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate;", ['fromDate' => $fromDate, 'toDate' => $toDate]);

        $storeType = self::getStoreType($store);
        if (in_array('gtf', $storeType)) {
            $dateTimeRangeTs = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq, true);
            $redisOrder = RedisGtf::getTotalOrderByDate($store, $dateTimeRangeTs['fromDate'], $dateTimeRangeTs['toDate']);
            $orders = (object)array("total" => $redisOrder['total'] + $orders->total);
            $totalAmount = (object)array("total" => $redisOrder['totalAmount'] + $totalAmount->total);

            if ($debug) {
                dump($rangeDate, $dateTimeRangeTs, $orders, $totalAmount);
            }
        }

        return array(
            'title' => self::$rangeDate[$rangeDate] ?? '',
            'dateDisplay' => self::getDateDisplay($rangeDate, $fromDate, $toDate),
            'fbAds' => array (
                'totalSpend' => $fbAds->totalSpend ?? 0,
                'totalUniqueClicks' => $fbAds->totalUniqueClicks ?? 0,
                'dailyBudget' => ($fbAds->dailyBudget ?? 0) + ($fbAdsets->dailyBudget ?? 0),
            ),
            'ggAds' => array (
                'ga_ad_cost' => $gaAds->ga_ad_cost ?? 0,
            ),
            'orders' => array(
                'total' => $orders->total ?? 0,
                'totalAmount' => $totalAmount->total ?? 0,
            ),
            'productCost' => $totalAmount->total * 0.38,
            'profitLoss' => $totalAmount->total - ($fbAds->totalSpend + $gaAds->ga_ad_cost) - $totalAmount->total * 0.38,
            'mo' => $totalAmount->total > 0 ? 100*(($fbAds->totalSpend + $gaAds->ga_ad_cost) / $totalAmount->total) : 0,
            'cpc' => $fbAds->totalUniqueClicks != 0 ? ($fbAds->totalSpend + $gaAds->ga_ad_cost) / $fbAds->totalUniqueClicks : 0,
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

    public static function getDatesByRangeDateLabel ($store = 'thecreattify', $rangeDate = 'today', $fromDateReq = '', $toDateReq = '', $returnTimestamp = false) {
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
        } elseif ($rangeDate == 'last_7_days') {
            $dateTimeStart->subDays(7);
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
                $dateTimeStart = Carbon::createFromFormat('Y-m-d', $fromDateReq, $phpTimeZone);
                $dateTimeEnd = Carbon::createFromFormat('Y-m-d', $toDateReq, $phpTimeZone);

                $fromDate = $dateTimeStart->format('Y-m-d');
                $toDate = $dateTimeEnd->format('Y-m-d 23:59:59');
            }
        }
        if ($returnTimestamp) {
            $newDateTimeStart = Carbon::createFromFormat('Y-m-d H:i:s', "$fromDate 00:00:00", $phpTimeZone);
            $newDateTimeEnd = Carbon::createFromFormat('Y-m-d H:i:s', $toDate, $phpTimeZone);

            return array (
                'fromDate' => $newDateTimeStart->getTimestampMs(),
                'toDate' => $newDateTimeEnd->getTimestampMs(),
            );
        } else {
            return array (
                'fromDate' => $fromDate,
                'toDate' => $toDate,
            );
        }

    }

    public static function getAccountsAdsReportByDate($store = 'thecreattify', $rangeDate = 'today', $fromDateReq = '', $toDateReq = '') {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $fbAds = DB::table('fb_campaign_insights')
            ->select(DB::raw('account_name, sum(spend) as totalSpend, sum(inline_link_clicks) as totalUniqueClicks, max(fb_accounts.account_status) as account_status'))
            ->leftJoin('fb_accounts', 'fb_accounts.name', '=', 'fb_campaign_insights.account_name')
            ->whereIn('account_id', $fbAccountIds)
            ->where('date_record', '>=', $fromDate)
            ->where('date_record', '<=', $toDate)
            ->groupBy('account_name')->get();

        $gaAds = DB::table('ga_campaign_reports')
            ->select(DB::raw('SUM(transactions) as ga_total_order,
                SUM(transaction_revenue) as ga_total_order_amount,
                SUM(ad_cost) as ga_ad_cost'))
            ->where('ga_campaign_reports.store', $store)
            ->where('ga_campaign_reports.date_record', '>=', $fromDate)
            ->where('ga_campaign_reports.date_record', '<=', $toDate)
            ->where('ga_campaign_reports.transactions', '>', 0)
            ->first();

        $result = array();
        foreach ($fbAds->all() as $acc) {
            $result[] = array(
                'account_status' => FbAccount::$status[$acc->account_status] ?? '',
                'account_name' => $acc->account_name,
                'totalSpend' => $acc->totalSpend,
                'cpc' => $acc->totalUniqueClicks != 0 ? $acc->totalSpend / $acc->totalUniqueClicks : 0,
            );
        }
        $result[] = array(
            'account_status' => 'ACTIVE',
            'account_name' => 'Google Ads',
            'totalSpend' => $gaAds->ga_ad_cost,
            'cpc' => 0,
        );
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

        $storeType = self::getStoreType($store);
        if (in_array('gtf', $storeType)) {
            $dateTimeRangeTs = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq, true);
            $redisOrder = RedisGtf::getAllOrderByDate($store, $dateTimeRangeTs['fromDate'], $dateTimeRangeTs['toDate']);
            $orders = array_merge($orders, $redisOrder);
        }

        $ordersResult = array();
        foreach ($orders as $o) {
            $o->country_code = $o->country_code ?? 'UNKNOWN';
            if (!isset($ordersResult[$o->country_code])) {
                $ordersResult[$o->country_code]['total_order'] = $o->total_order;
                $ordersResult[$o->country_code]['total_order_amount'] = $o->total_order_amount;
            } else {
                $ordersResult[$o->country_code]['total_order'] += $o->total_order;
                $ordersResult[$o->country_code]['total_order_amount'] += $o->total_order_amount;
            }
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
            select count(Distinct ol.order_id) as total_order, pt.product_type_name, MAX(pt.product_type_code) as product_type_code, sum((ol.price*ol.quantity) - ol.total_discount)/$radioCurrency as total_order_amount, sum(ol.quantity) as total_quantity
            from orders o
            left join order_line_items ol ON o.shopify_id = ol.order_id
            left join products p on ol.product_id = p.shopify_id and p.store = '$store'
            left join shopify_product_type pt on p.product_type = pt.product_type_name
            where o.store = '$store' and p.shopify_id > 0 and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate
            group by p.product_type;
            ;"
            , ['fromDate' => $fromDate, 'toDate' => $toDate]
        );

        $storeType = self::getStoreType($store);
        if (in_array('gtf', $storeType)) {
            $dateTimeRangeTs = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq, true);
            $redisOrder = RedisGtf::getAllOrderLinesByDate($store, $dateTimeRangeTs['fromDate'], $dateTimeRangeTs['toDate']);
            $orders = array_merge($orders, $redisOrder);
        }

        $ordersTips = DB::select("
            select count(Distinct ol.order_id) as total_order, 'TIP' as product_type_name, 'TIP' as product_type_code, sum((ol.price*ol.quantity) - ol.total_discount)/$radioCurrency as total_order_amount, sum(ol.quantity) as total_quantity
            from orders o
            left join order_line_items ol ON o.shopify_id = ol.order_id
            where o.store = '$store' and ol.name like 'TIP %' and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate
            ;"
            , ['fromDate' => $fromDate, 'toDate' => $toDate]
        );

        $ordersShipping = DB::select("
            select count(o.id) as total_order, 'SHIPPING_FEE' as product_type_name, 'SHIPPING_FEE' as product_type_code, sum(total_price - subtotal_price - total_tip_received)/$radioCurrency as total_order_amount, count(o.id) as total_quantity
            from orders o
            where o.store = '$store' and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate
            ;"
            , ['fromDate' => $fromDate, 'toDate' => $toDate]
        );

        $orders = array_merge($orders, $ordersTips, $ordersShipping);

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
        preg_match('/.*Type(\w{0,7})/', $campaignName, $result);
        $productType = $result[1] ?? '';

        if (!$productType) {
            $result = array();
            preg_match('/([A-Z]{2,7}) carousel/', $campaignName, $result);
            $productType = $result[1] ?? '';
        }

        if (!$productType) {
            $result = array();
            preg_match('/[A-Z]{2,3}\d{3,5}([A-Z]{2,7})D\d{1,2}/', $campaignName, $result);
            $productType = $result[1] ?? '';
        }

        if (!$productType) {
            $result = array();
            preg_match('/[A-Z]{2,3}\d{3,5}([A-Z]{2,7})D?\d{0,2}/', $campaignName, $result);
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
            select count(Distinct ol.order_id) as total_order, sku, sum((ol.price*ol.quantity) - ol.total_discount)/$radioCurrency as total_order_amount, SUM(ol.quantity) as total_quantity
            from orders o
            left join order_line_items ol ON o.shopify_id = ol.order_id
            where o.store = '$store' and ol.product_id > 0 and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate
            group by ol.sku;
            ;"
            , ['fromDate' => $fromDate, 'toDate' => $toDate]
        );

        $storeType = self::getStoreType($store);
        if (in_array('gtf', $storeType)) {
            $dateTimeRangeTs = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq, true);
            $redisOrder = RedisGtf::getAllOrderLinesByDate($store, $dateTimeRangeTs['fromDate'], $dateTimeRangeTs['toDate']);
            $orders = array_merge($orders, $redisOrder);
        }

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
        preg_match('/[A-Z]{2,3}\d{3,5}[A-Z]{2,7}(D\d{1,2})/', $campaignName, $result);
        $designer = isset($result[1]) ? strtoupper($result[1]) : '';

        if (!$designer) {
            preg_match('/[A-Z]{2,3}\d{3,5}[A-Z]{2,7}(D?\d{0,2})/', $campaignName, $result);
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
            preg_match('/[A-Z]{2,3}\d{3,5}[A-Z]{2,7}(D\d{1,2})/', $sku, $result);
            $designer = isset($result[1]) ? strtoupper($result[1]) : '';
        }

        if (!$designer) {
            preg_match('/[A-Z]{2,3}\d{3,5}[A-Z]{2,7}(D?\d{0,2})/', $sku, $result);
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
            select count(Distinct ol.order_id) as total_order, sku, sum((ol.price*ol.quantity) - ol.total_discount)/$radioCurrency as total_order_amount, sum(ol.quantity) as total_quantity
            from orders o
            left join order_line_items ol ON o.shopify_id = ol.order_id
            where o.store = '$store' and ol.product_id > 0 and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(o.shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate
            group by ol.sku;
            ;"
            , ['fromDate' => $fromDate, 'toDate' => $toDate]
        );

        $storeType = self::getStoreType($store);
        if (in_array('gtf', $storeType)) {
            $dateTimeRangeTs = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq, true);
            $redisOrder = RedisGtf::getAllOrderLinesByDate($store, $dateTimeRangeTs['fromDate'], $dateTimeRangeTs['toDate']);
            $orders = array_merge($orders, $redisOrder);
        }

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
        preg_match('/([A-Z]{2,3})\d{3,5}[A-Z]{2,7}D\d{1,2}/', $campaignName, $result);
        $idea = isset($result[1]) ? strtoupper($result[1]) : '';

        if (!$idea) {
            preg_match('/([A-Z]{2,3})\d{3,5}[A-Z]{2,7}D?\d{0,2}/', $campaignName, $result);
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
            preg_match('/^([A-Z]{2,3})\d{3,5}[A-Z]{2,7}D\d{1,2}/', $sku, $result);
            $idea = isset($result[1]) ? strtoupper($result[1]) : '';
        }

        if (!$idea) {
            preg_match('/([A-Z]{2,3})\d{3,5}[A-Z]{2,7}D?\d{0,2}/', $sku, $result);
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

        $adsStaffTable = DB::table('gifttify_code')->where('type', '=', 'ads_staff')->get();
        $adsStaffData = $adsStaffTable->keyBy('code')->all();

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
                $adsResult[$adsStaff]['adsStaff'] = isset($adsStaffData[$adsStaff]) ? $adsStaffData[$adsStaff]->name : ($adsStaff != 'UNKNOWN' ? $adsStaff : 'UNKNOWN');

                $adsResult[$adsStaff]['totalSpend'] = 0;
                $adsResult[$adsStaff]['totalCamp'] = 0;
            }
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
        $adsStaff = "UNKNOWN";
        if (preg_match('/.*hoai.*/', strtolower($campaignName), $result)) {
            $adsStaff = 'Hoài';
        } elseif (preg_match('/.*tien.*/', strtolower($campaignName), $result)) {
            $adsStaff = 'Tiến';
        } elseif (preg_match('/.*hoang.*/', strtolower($campaignName), $result)) {
            $adsStaff = 'Hoàng';
        } elseif (preg_match('/^m .*/', strtolower($campaignName), $result)) {
            $adsStaff = 'Minh';
        } elseif (preg_match('/.* minh .*/', strtolower($campaignName), $result)) {
            $adsStaff = 'Minh';
        } elseif (preg_match('/^([A-Z]{3})\s.*/', $campaignName, $result)) {
            $adsStaff = isset($result[1]) ? $result[1] : '';
        }
        return $adsStaff;
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
            } elseif ($type == 'campaign_name') {
                $checkCode = $v->campaign_name;
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

    public static function getCampaignInfoByDate ($store = 'thecreattify', $rangeDate = 'today', $fromDateReq = '', $toDateReq = '',$debug = 0) {
        $storeConfig = self::getStoreConfig($store);
        if (!$storeConfig) return false;

        $fbAccountIds = $storeConfig['fbAccountIds'];
        $mysqlTimeZone = $storeConfig['mysqlTimeZone'];
        $radioCurrency = $storeConfig['radioCurrency'];

        $dateTimeRange = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq);
        $fromDate = $dateTimeRange['fromDate'];
        $toDate = $dateTimeRange['toDate'];

        $fbAds = DB::table('fb_ads_insights')
            ->select(DB::raw('
                MAX(fb_ads_insights.account_name) as account_name ,fb_ads_insights.campaign_name, SUM(fb_ads_insights.impressions) as impressions,
                SUM(spend) as totalSpend, sum(inline_link_clicks) as totalUniqueClicks,
                CASE
                    WHEN (select SUM(daily_budget/100) from fb_ad_sets where campaign_id=fb_ads_insights.campaign_id ) > 0
                        THEN (select SUM(daily_budget/100) from fb_ad_sets where campaign_id=fb_ads_insights.campaign_id )
                    ELSE (select SUM(daily_budget/100) from fb_campaigns where fb_campaign_id=fb_ads_insights.campaign_id)
                END as budget,
                (select status from fb_campaigns where fb_campaign_id=fb_ads_insights.campaign_id) as status,
                (select account_status from fb_accounts where id=fb_ads_insights.account_id) as account_status,
                MAX(transactions) as ga_total_order,
                MAX(transaction_revenue) as ga_total_order_amount
            '))
            ->leftJoin('ga_campaign_reports', function($join) use ($store) {
                $join->on('fb_ads_insights.campaign_name', '=', 'ga_campaign_reports.campaign_name');
                $join->on('ga_campaign_reports.date_record', '=', 'fb_ads_insights.date_record');
                $join->where('ga_campaign_reports.store', '=', $store);
            })
            ->whereIn('fb_ads_insights.account_id', $fbAccountIds)
            ->where('fb_ads_insights.date_record', '>=', $fromDate)
            ->where('fb_ads_insights.date_record', '<=', $toDate)
            ->groupBy(array('account_id', 'campaign_name', 'campaign_id'))->get();

        $gaAds = DB::table('ga_campaign_reports')
            ->select(DB::raw('campaign_name, SUM(transactions) as ga_total_order,
                SUM(transaction_revenue) as ga_total_order_amount,
                SUM(ad_cost) as ga_ad_cost'))
            ->where('ga_campaign_reports.store', $store)
            ->where('ga_campaign_reports.date_record', '>=', $fromDate)
            ->where('ga_campaign_reports.date_record', '<=', $toDate)
            ->where('ga_campaign_reports.transactions', '>', 0)
            ->groupBy(array('campaign_name'))
            ->get();

        $adsResult = array();
        foreach ($fbAds->all() as $v) {
            if (!isset($adsResult[$v->campaign_name])) {
                $adsResult[$v->campaign_name]['account_status'] = $v->account_status;
                $adsResult[$v->campaign_name]['account_name'] = $v->account_name;
                $adsResult[$v->campaign_name]['campaign_name'] = $v->campaign_name;
                $adsResult[$v->campaign_name]['status'] = $v->status;
                $adsResult[$v->campaign_name]['totalSpend'] = 0;
                $adsResult[$v->campaign_name]['totalUniqueClicks'] = 0;
                $adsResult[$v->campaign_name]['impressions'] = 0;
                $adsResult[$v->campaign_name]['budget'] = 0;
                $adsResult[$v->campaign_name]['ga_total_order'] = 0;
                $adsResult[$v->campaign_name]['ga_total_order_amount'] = 0;
                $adsResult[$v->campaign_name]['ga_ad_cost'] = 0;
            }
            $adsResult[$v->campaign_name]['totalSpend'] += $v->totalSpend;
            $adsResult[$v->campaign_name]['totalUniqueClicks'] += $v->totalUniqueClicks;
            $adsResult[$v->campaign_name]['impressions'] += $v->impressions;
            $adsResult[$v->campaign_name]['budget'] += $v->budget;
            $adsResult[$v->campaign_name]['cpc'] = ($adsResult[$v->campaign_name]['totalUniqueClicks'] != 0 ? $adsResult[$v->campaign_name]['totalSpend'] / $adsResult[$v->campaign_name]['totalUniqueClicks'] : 0);
            $adsResult[$v->campaign_name]['cpm'] = ($adsResult[$v->campaign_name]['impressions'] >= 1 ? 1000 * $adsResult[$v->campaign_name]['totalSpend'] / $adsResult[$v->campaign_name]['impressions'] : 0);
        }

        foreach ($gaAds->all() as $v) {
            $v->campaign_name = ($v->campaign_name == '(not set)' || $v->campaign_name == '') ? 'UNKNOWN' : $v->campaign_name;
            if (!isset($adsResult[$v->campaign_name])) {
                $adsResult[$v->campaign_name]['account_status'] = '';
                $adsResult[$v->campaign_name]['account_name'] = '';
                $adsResult[$v->campaign_name]['campaign_name'] = $v->campaign_name;
                $adsResult[$v->campaign_name]['status'] = '';
                $adsResult[$v->campaign_name]['totalSpend'] = 0;
                $adsResult[$v->campaign_name]['totalUniqueClicks'] = 0;
                $adsResult[$v->campaign_name]['impressions'] = 0;
                $adsResult[$v->campaign_name]['budget'] = 0;
                $adsResult[$v->campaign_name]['ga_total_order'] = 0;
                $adsResult[$v->campaign_name]['ga_total_order_amount'] = 0;
                $adsResult[$v->campaign_name]['ga_ad_cost'] = 0;
            }
            $adsResult[$v->campaign_name]['ga_total_order'] += $v->ga_total_order;
            $adsResult[$v->campaign_name]['ga_total_order_amount'] += $v->ga_total_order_amount;
            $adsResult[$v->campaign_name]['ga_ad_cost'] += $v->ga_ad_cost;
        }

        $orders = DB::select("select name, note_attributes,1 as total_order, (total_price)/$radioCurrency as total_order_amount from orders where store='$store' and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') >= :fromDate and CONVERT_TZ(shopify_created_at,'UTC','$mysqlTimeZone') <= :toDate;"
            , ['fromDate' => $fromDate, 'toDate' => $toDate]
        );

        $storeType = self::getStoreType($store);
        if (in_array('gtf', $storeType)) {
            $dateTimeRangeTs = self::getDatesByRangeDateLabel($store, $rangeDate, $fromDateReq, $toDateReq, true);
            $redisOrder = RedisGtf::getAllOrderByDate($store, $dateTimeRangeTs['fromDate'], $dateTimeRangeTs['toDate']);
            $orders = array_merge($orders, $redisOrder);
        }
        $ordersResult = array();
        foreach ($orders as $o) {
            $note_attributes = json_decode($o->note_attributes);
            $campaign_name = 'UNKNOWN';
            if ($note_attributes) {
                foreach ($note_attributes as $note) {
                    if ($note->name == 'utm_campaign') {
                        $campaign_name = $note->value ?? 'UNKNOWN';
                        break;
                    }
                }
            }
            if ($debug == 1 && ($campaign_name == 'UNKNOWN' || $campaign_name == '')) {
                dump($o->name);
            }

            if (!isset($ordersResult[$campaign_name])) {
                $ordersResult[$campaign_name]['campaign_name'] = $campaign_name ?? 'UNKNOWN';
                $ordersResult[$campaign_name]['total_order'] = 0;
                $ordersResult[$campaign_name]['total_order_amount'] = 0;
            }
            $ordersResult[$campaign_name]['total_order'] += $o->total_order;
            $ordersResult[$campaign_name]['total_order_amount'] += $o->total_order_amount;
        }

        $campaignReports = array_merge(array_keys($ordersResult) , array_keys($adsResult));
        $campaignReports = array_unique($campaignReports);

        $result = array();
        foreach ($campaignReports as $v) {
            $result[$v]['campaign_name'] = $v ?: 'UNKNOWN';
            $result[$v]['total_order_amount'] = $ordersResult[$v]['total_order_amount'] ?? 0;
            $result[$v]['total_order'] = $ordersResult[$v]['total_order'] ?? 0;
            $result[$v]['totalSpend'] = $adsResult[$v]['totalSpend'] ?? 0;
            $result[$v]['cpc'] = $adsResult[$v]['cpc'] ?? 0;
            $result[$v]['cpm'] = $adsResult[$v]['cpm'] ?? 0;
            $result[$v]['budget'] = $adsResult[$v]['budget'] ?? 0;
            $result[$v]['totalUniqueClicks'] = $adsResult[$v]['totalUniqueClicks'] ?? 0;
            $result[$v]['impressions'] = $adsResult[$v]['impressions'] ?? 0;
            $result[$v]['account_name'] = $adsResult[$v]['account_name'] ?? '';
            $result[$v]['status'] = $adsResult[$v]['status'] ?? '';
            $result[$v]['account_status'] = $adsResult[$v]['account_status'] ?? '';
            $result[$v]['ga_total_order'] = $adsResult[$v]['ga_total_order'] ?? 0;
            $result[$v]['ga_total_order_amount'] = $adsResult[$v]['ga_total_order_amount'] ?? 0;
            $result[$v]['ga_ad_cost'] = $adsResult[$v]['ga_ad_cost'] ?? 0;
            $result[$v]['mo'] = ($result[$v]['total_order_amount']) > 0 ? 100*(($result[$v]['totalSpend']+$result[$v]['ga_ad_cost']) / $result[$v]['total_order_amount']) : 0;
        }
        usort($result, [self::class, 'sort_result_by_ads_cost']);
        return $result;

    }

    public static function getAccounts () {
        return DB::table('fb_accounts')->get();
    }

}
