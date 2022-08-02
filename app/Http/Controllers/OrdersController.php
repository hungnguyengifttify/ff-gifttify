<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Signifly\Shopify\Shopify;
use App\Models\Orders;
use App\Models\Dashboard;

class OrdersController extends Controller {

    private $shopify;

    public function __construct(Shopify $shopify)
    {
        $this->shopify = $shopify;
    }

    public function index(Request $request)
    {
        $stores = array('us', 'au-thecreattify', 'singlecloudy');

        foreach ($stores as $store) {
            $shopifyConfig = Dashboard::getShopifyConfig($store);

            $apiKey = $shopifyConfig['apiKey'];
            $password = $shopifyConfig['password'];
            $domain = $shopifyConfig['domain'];
            $apiVersion = $shopifyConfig['apiVersion'];
            $dateTimeZone = $shopifyConfig['dateTimeZone'];

            $dateTime = new \DateTime("now", $dateTimeZone);
            $dateTime->modify('-10 day');
            $createdAtMin = $dateTime->format('Y-m-d');
            $createdAtMin = '1900-01-01';
            /*$timeReport = $this->argument('time_report') ?? '';
            if ($timeReport == 'all') {
                $updatedAtMin = '1900-01-01';
            }*/

            $shopify = new Shopify($apiKey, $password, $domain, $apiVersion);
            $products = $shopify->paginateProducts([
                'created_at_min' => $createdAtMin,
                'limit' => 250
            ]);

            foreach ($products as $p) {
                $values = $p->toArray();
                foreach ($values as $v) {
                    dd($v);
                    $v['shopify_id'] = $v['id'] ?? 0;
                    $v['shopify_created_at'] = $v['created_at'] ?? '1000-01-01';
                    $v['shopify_updated_at'] = $v['updated_at'] ?? '1000-01-01';

                }
            }
        }


    }

    protected function processFlatOrdersByOrderItems(array $orders): array
    {
        $flatOrders = [];
        $listPropertyNameAppendToOrderItemName = ['material', 'size', 'color'];
        foreach ($orders as $order){
            foreach($order['line_items'] as $orderItem){
                $orderItemName = trim($orderItem['name']);
                if (preg_match('/- Options$/i', $orderItemName)){
                    continue;
                }
                if($orderItemName == 'T' || strtolower($orderItemName) == 'tip'){
                    continue;
                }
                if(!empty($orderItem['properties'])){
                    $listPropertyValueAppendToOrderItemName = [];
                    foreach($orderItem['properties'] as $property){
                        if(in_array($property['name'], $listPropertyNameAppendToOrderItemName)){
                            $listPropertyValueAppendToOrderItemName[] = $property['value'];
                        }
                    }
                    if(count($listPropertyValueAppendToOrderItemName) > 0){
                        $orderItemName .= ' - ' . join(' / ', $listPropertyValueAppendToOrderItemName);
                    }
                }
                $shippingAddress = $order['shipping_address'];
                $shippingAddress['street'] = join(', ', [
                    $shippingAddress['address1'],
                    $shippingAddress['address2']
                ]);
                $flatOrders[] = [
                    'name' => $order['name'],
                    'email' => $order['email'],
                    'processed_at' => $order['processed_at'],
                    'shipping_address' => $shippingAddress,
                    'note' => $order['note'],
                    'order_item_quantity' => $orderItem['quantity'],
                    'order_item_name' => $orderItemName,
                ];
            }
        }
        return array_reverse($flatOrders);
    }
}
