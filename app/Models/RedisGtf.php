<?php

namespace App\Models;

use App\Services\RemixApi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use FKRediSearch\Query\Query;
use FKRediSearch\Query\QueryBuilder;
use FKRediSearch\Setup;
use Illuminate\Support\Facades\Config;

class RedisGtf extends Model
{
    public static function getTotalOrderByDate($store = 'thecreattify', $fromDateTs = '', $toDateTs = '') {
        $redisConfig = Config::get("database.redis.$store");
        $client = Setup::connect( $redisConfig['host'], $redisConfig['port'], $redisConfig['password'], 0 );
        $search = new Query( $client, 'idx:order' );

        $query = new QueryBuilder();
        $query->setTokenize()
            ->setFuzzyMatching()
            ->addCondition('status', ['completed'], 'AND', TRUE);

        $fromDate = $fromDateTs * 1;
        $toDate = $toDateTs * 1;

        $results = $search
            ->sortBy( 'paidAt', $order = 'DESC' )
            ->numericFilter( 'paidAt', $fromDate, $toDate )
            ->limit( 0, $pageSize = 100000 ) // If set, we limit the results to the offset and number of results given. The default is 0 10
            ->search( $query, $documentsAsArray = true );

        $total = $results->getCount();
        if ($results->getCount() == 0) {
            return array('total' => 0, 'totalAmount' => 0);
        }

        $totalAmount = 0;
        foreach ($results->getDocuments() as $k => $v) {
            $order = json_decode($v['$']);
            $totalAmount += ($order->total / $order->currency->rate);
        }
        return array('total' => $total, 'totalAmount' => $totalAmount);
    }

    public static function getAllOrderByDate($store = 'thecreattify', $fromDateTs = '', $toDateTs = '') {
        $redisConfig = Config::get("database.redis.$store");
        $client = Setup::connect( $redisConfig['host'], $redisConfig['port'], $redisConfig['password'], 0 );
        $search = new Query( $client, 'idx:order' );

        $query = new QueryBuilder();
        $query->setTokenize()
            ->setFuzzyMatching()
            ->addCondition('status', ['completed'], 'AND', TRUE);

        $fromDate = $fromDateTs * 1;
        $toDate = $toDateTs * 1;

        $ordersResult = array();

        $results = $search
            ->sortBy( 'paidAt', $order = 'DESC' )
            ->numericFilter( 'paidAt', $fromDate, $toDate )
            ->limit( 0, $pageSize = 100000 ) // If set, we limit the results to the offset and number of results given. The default is 0 10
            ->search( $query, $documentsAsArray = true );

        if ($results->getCount() == 0) {
            $ordersResult[] = array('total_order' => 0, 'total_order_amount' => 0, 'name' => 'gft');
            return $ordersResult;
        }
        foreach ($results->getDocuments() as $k => $v) {
            $order = json_decode($v['$']);
            $totalAmount = ($order->total / $order->currency->rate);
            $noteAttributesArr = array();
            $noteAttributesArr[] = (object)array('name' => 'utm_campaign', 'value' => $order->utmCampaign ?? 'UNKNOWN' );
            $ordersResult[] = (object)array(
                'name' => $order->id,
                'country_code' => $order->address->country ?? 'UNKNOWN',
                'total_order' => 1,
                'total_order_amount' => $totalAmount,
                'note_attributes' => json_encode($noteAttributesArr)
            );
        }
        return $ordersResult;
    }

    public static function getAllOrderLinesByDate($store = 'thecreattify', $fromDateTs = '', $toDateTs = '') {
        $redisConfig = Config::get("database.redis.$store");
        $client = Setup::connect( $redisConfig['host'], $redisConfig['port'], $redisConfig['password'], 0 );
        $search = new Query( $client, 'idx:order' );

        $query = new QueryBuilder();
        $query->setTokenize()
            ->setFuzzyMatching()
            ->addCondition('status', ['completed'], 'AND', TRUE);

        $fromDate = $fromDateTs * 1;
        $toDate = $toDateTs * 1;

        $ordersResult = array();

        $results = $search
            ->sortBy( 'paidAt', $order = 'DESC' )
            ->numericFilter( 'paidAt', $fromDate, $toDate )
            ->limit( 0, $pageSize = 100000 ) // If set, we limit the results to the offset and number of results given. The default is 0 10
            ->search( $query, $documentsAsArray = true );

        if ($results->getCount() == 0) {
            $ordersResult[] = array(
                'sku' => '',
                'product_type_name' => '',
                'product_type_code' => '',
                'total_order' => 0,
                'total_order_amount' => 0,
                'total_quantity' => 0,
                'name' => 'gft'
            );
            return $ordersResult;
        }
        foreach ($results->getDocuments() as $k => $v) {
            $order = json_decode($v['$']);

            $ordersResult[] = (object)array(
                'sku' => '',
                'name' => $order->id,
                'product_type_name' => 'TIP',
                'product_type_code' => 'TIP',
                'total_order' => 1,
                'total_quantity' => 1,
                'total_order_amount' => ($order->tip / $order->currency->rate),
            );

            $ordersResult[] = (object)array(
                'sku' => '',
                'name' => $order->id,
                'product_type_name' => 'SHIPPING_FEE',
                'product_type_code' => 'SHIPPING_FEE',
                'total_order' => 1,
                'total_quantity' => 1,
                'total_order_amount' => ($order->shippingTotal / $order->currency->rate),
            );

            foreach ($order->items as $item) {
                $ordersResult[] = (object)array(
                    'sku' => $item->variant->sku,
                    'product_type_name' => $item->product->productType ?? 'UNKNOWN',
                    'product_type_code' => $item->product->productType ?? 'UNKNOWN',
                    'name' => $order->id,
                    'country_code' => $order->address->country ?? 'UNKNOWN',
                    'total_order' => 1/count($order->items),
                    'total_quantity' => $item->quantity,
                    'total_order_amount' => ($item->price * $item->quantity) / $order->currency->rate,
                );
            }

        }
        return $ordersResult;
    }

    public static function getRedisOrdersList($db = 1, $dateRanges = array(), $status = '', $page = 1, $limit = 10, $isArray = false) {
        $queryArr = array(
            'db' => $db,
            'dateRanges' => $dateRanges,
            'status' => $status,
            'keyword' => '',
            'page' => $page,
            'limit' => $limit,
        );
        $query = http_build_query($queryArr);

        $remixApi = new RemixApi();
        $response = $remixApi->request('GET', 'orders?' . $query, null, null);
        if ($response && $response->getStatusCode() == '200') {
            $res = $response->getBody()->getContents();
            return json_decode($res, $isArray);
        }
        return false;
    }

    public static  function getRedisProductsList($db = 1, $page = 1, $limit = 10) {
        $queryArr = array(
            'db' => $db,
            'keyword' => '',
            'tag' => '',
            'productType' => '',
            'status' => 'publish',
            'currency' => '',
            'fields' => '',
            'page' => $page,
            'limit' => $limit,
        );
        $query = http_build_query($queryArr);

        $remixApi = new RemixApi();
        $response = $remixApi->request('GET', 'products?' . $query, null, null);
        if ($response && $response->getStatusCode() == '200') {
            $res = $response->getBody()->getContents();
            return json_decode($res);
        }
        return false;
    }

    public static  function getRedisProductDetail($db = 1, $id) {
        if (!$id) return false;

        $queryArr = array(
            'db' => $db,
            'country' => '',
            'currency' => ''
        );
        $query = http_build_query($queryArr);

        $remixApi = new RemixApi();
        $response = $remixApi->request('GET', "products/variable/$id?" . $query);
        if ($response && $response->getStatusCode() == '200') {
            $res = $response->getBody()->getContents();
            return json_decode($res);
        }
        return false;
    }
}
