<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Signifly\Shopify\Shopify;

class OrdersController extends Controller {

    private $shopify;

    public function __construct(Shopify $shopify)
    {
        $this->shopify = $shopify;
    }

    public function index(Request $request)
    {
        $limit = (int)($request->get('limit')?:10);
        $orders = $this->shopify->getOrders([
            'limit' => $limit,
            'fields' => 'id,name,processed_at,email,note,shipping_address,created_at,line_items'
        ]);
        $total = $this->shopify->getOrdersCount();
        //echo "<pre/>";
        //print_r($orders->toArray());
        //exit;
        $orders = $this->processFlatOrdersByOrderItems($orders->toArray());
        return view('orders', compact('orders', 'limit', 'total'));
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
