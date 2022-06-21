<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Signifly\Shopify\Shopify;
use App\Models\Orders;

class OrdersController extends Controller {

    private $shopify;

    public function __construct(Shopify $shopify)
    {
        $this->shopify = $shopify;
    }

    public function index(Request $request)
    {
        $limit = (int)($request->get('limit')?:2);
        $totalPage = (int)($request->get('total_page')?:3);
        $orders = $this->shopify->paginateOrders([
            'limit' => $limit
        ]);

        $i = 0;
        foreach ($orders as $o) {
            $i++;
            if ($i > $totalPage) {
                break;
            }

            $values = $o->toArray();
            foreach ($values as $v) {
                $v['shopify_id'] = $v['id'] ?? 0;
                $v['shopify_created_at'] = $v['created_at'] ?? '1000-01-01';
                $v['shopify_updated_at'] = $v['updated_at'] ?? '1000-01-01';

                Orders::updateOrCreate([
                    'shopify_id' => $v['shopify_id'] ?? 0,
                ], [
                    'store' => $v['store'] ?? '',
                    'shopify_id' => $v['shopify_id'] ?? 0,
                    'admin_graphql_api_id' => $v['admin_graphql_api_id'] ?? '',
                    'app_id' => $v['app_id'] ?? 0,
                    'browser_ip' => $v['browser_ip'] ?? '',
                    'buyer_accepts_marketing' => $v['buyer_accepts_marketing'] ?? 0,
                    'cancel_reason' => $v['cancel_reason'] ?? '',
                    'cancelled_at' => $v['cancelled_at'] ?? '1000-01-01',
                    'cart_token' => $v['cart_token'] ?? '',
                    'checkout_id' => $v['checkout_id'] ?? 0,
                    'checkout_token' => $v['checkout_token'] ?? '',
                    'client_details' => json_encode($v['client_details']) ?? '',
                    'closed_at' => $v['closed_at'] ?? '1000-01-01',
                    'confirmed' => $v['confirmed'] ?? 0,
                    'contact_email' => $v['contact_email'] ?? '',
                    'shopify_created_at' => $v['shopify_created_at'] ?? '1000-01-01',
                    'currency' => $v['currency'] ?? '',
                    'current_subtotal_price' => $v['current_subtotal_price'] ?? 0,
                    'current_subtotal_price_set' => json_encode($v['current_subtotal_price_set']) ?? '',
                    'current_total_discounts' => $v['current_total_discounts'] ?? 0,
                    'current_total_discounts_set' => json_encode($v['current_total_discounts_set']) ?? '',
                    'current_total_duties_set' => json_encode($v['current_total_duties_set']) ?? '',
                    'current_total_price' => $v['current_total_price'] ?? 0,
                    'current_total_price_set' => json_encode($v['current_total_price_set']) ?? '',
                    'current_total_tax' => $v['current_total_tax'] ?? 0,
                    'current_total_tax_set' => json_encode($v['current_total_tax_set']) ?? '',
                    'customer_locale' => $v['customer_locale'] ?? '',
                    'device_id' => $v['device_id'] ?? '',
                    'discount_codes' => json_encode($v['discount_codes']) ?? '',
                    'email' => $v['email'] ?? '',
                    'estimated_taxes' => $v['estimated_taxes'] ?? '',
                    'financial_status' => $v['financial_status'] ?? '',
                    'fulfillment_status' => $v['fulfillment_status'] ?? '',
                    'gateway' => $v['gateway'] ?? '',
                    'landing_site' => $v['landing_site'] ?? '',
                    'landing_site_ref' => $v['landing_site_ref'] ?? '',
                    'location_id' => $v['location_id'] ?? '',
                    'name' => $v['name'] ?? '',
                    'note' => $v['note'] ?? '',
                    'note_attributes' => json_encode($v['note_attributes']) ?? '',
                    'number' => $v['number'] ?? 0,
                    'order_number' => $v['order_number'] ?? 0,
                    'order_status_url' => $v['order_status_url'] ?? '',
                    'original_total_duties_set' => json_encode($v['original_total_duties_set']) ?? '',
                    'payment_gateway_names' => json_encode($v['payment_gateway_names']) ?? '',
                    'phone' => $v['phone'] ?? '',
                    'presentment_currency' => $v['presentment_currency'] ?? '',
                    'processed_at' => $v['processed_at'] ?? '1000-01-01',
                    'processing_method' => $v['processing_method'] ?? '',
                    'reference' => $v['reference'] ?? '',
                    'source_identifier' => $v['source_identifier'] ?? '',
                    'source_name' => $v['source_name'] ?? '',
                    'source_url' => $v['source_url'] ?? '',
                    'subtotal_price' => $v['subtotal_price'] ?? 0,
                    'subtotal_price_set' => json_encode($v['subtotal_price_set']) ?? '',
                    'tags' => $v['tags'] ?? '',
                    'tax_lines' => json_encode($v['tax_lines']) ?? '',
                    'taxes_included' => $v['taxes_included'] ?? '',
                    'test' => $v['test'] ?? '',
                    'token' => $v['token'] ?? '',
                    'total_discounts' => $v['total_discounts'] ?? 0,
                    'total_discounts_set' => json_encode($v['total_discounts_set']) ?? '',
                    'total_line_items_price' => $v['total_line_items_price'] ?? 0,
                    'total_line_items_price_set' => json_encode($v['total_line_items_price_set']) ?? '',
                    'total_outstanding' => $v['total_outstanding'] ?? 0,
                    'total_price' => $v['total_price'] ?? 0,
                    'total_price_set' => json_encode($v['total_price_set']) ?? '',
                    'total_price_usd' => $v['total_price_usd'] ?? 0,
                    'total_shipping_price_set' => json_encode($v['total_shipping_price_set']) ?? '',
                    'total_tax' => $v['total_tax'] ?? 0,
                    'total_tax_set' => json_encode($v['total_tax_set']) ?? '',
                    'total_tip_received' => $v['total_tip_received'] ?? 0,
                    'total_weight' => $v['total_weight'] ?? 0,
                    'shopify_updated_at' => $v['shopify_updated_at'] ?? '1000-01-01',
                    'user_id' => $v['user_id'] ?? '',
                    'customer' => json_encode($v['customer']) ?? '',
                    'discount_applications' => json_encode($v['discount_applications']) ?? '',
                    'fulfillments' => json_encode($v['fulfillments']) ?? '',
                    'line_items' => json_encode($v['line_items']) ?? '',
                    'payment_terms' => $v['payment_terms'] ?? '',
                    'refunds' => json_encode($v['refunds']) ?? '',
                    'shipping_address' => json_encode($v['shipping_address']) ?? '',
                    'shipping_lines' => json_encode($v['shipping_lines']) ?? '',
                ]);
            }
        }
        dd($i);
die('VVVBBB');

        //$total = $this->shopify->getOrdersCount();
        echo "<pre/>";
        print_r( $this->shopify->getLastResponse() );
        exit;
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
