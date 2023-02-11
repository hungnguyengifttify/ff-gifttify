<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dashboard;
use App\Models\Orders;
use App\Models\OrderLineItems;
use Signifly\Shopify\Shopify;

class UpdateOrders extends Command
{
    /**
     * The name and signature of the console command.
     * time ['all', 'today']
     * @var string
     */
    protected $signature = 'orders:update {time_report?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Orders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job Update Orders running at ". now());
        $stores = Dashboard::getShopifyStoresList();

        foreach ($stores as $store) {
            $shopifyConfig = Dashboard::getShopifyConfig($store);
            if(in_array($store, ['hippiesy'])) continue;
            $apiKey = $shopifyConfig['apiKey'];
            $password = $shopifyConfig['password'];
            $domain = $shopifyConfig['domain'];
            $apiVersion = $shopifyConfig['apiVersion'];
            $dateTimeZone = $shopifyConfig['dateTimeZone'];

            $dateTime = new \DateTime("now", $dateTimeZone);
            $dateTime->modify( '-1 day' );
            $createdAtMin = $dateTime->format('Y-m-d');

            $timeReport = $this->argument('time_report') ?? '';
            if ($timeReport == 'all') {
                $createdAtMin = '1900-01-01';
            }

            $shopify = new Shopify($apiKey, $password, $domain, $apiVersion);
            $orders = $shopify->paginateOrders([
                'status' => 'any',
                'created_at_min' => $createdAtMin,
                'limit' => 250
            ]);

            foreach ($orders as $o) {
                $values = $o->toArray();
                foreach ($values as $v) {
                    $v['shopify_id'] = $v['id'] ?? 0;
                    $v['shopify_created_at'] = $v['created_at'] ?? '1000-01-01';
                    $v['shopify_updated_at'] = $v['updated_at'] ?? '1000-01-01';

                    Orders::updateOrCreate([
                        'store' => $store ?? '',
                        'shopify_id' => $v['shopify_id'] ?? 0,
                    ], [
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
                        'client_details' => json_encode($v['client_details'] ?? '') ?? '',
                        'closed_at' => $v['closed_at'] ?? '1000-01-01',
                        'confirmed' => $v['confirmed'] ?? 0,
                        'contact_email' => $v['contact_email'] ?? '',
                        'shopify_created_at' => $v['shopify_created_at'] ?? '1000-01-01',
                        'currency' => $v['currency'] ?? '',
                        'current_subtotal_price' => $v['current_subtotal_price'] ?? 0,
                        'current_subtotal_price_set' => json_encode($v['current_subtotal_price_set'] ?? '') ?? '',
                        'current_total_discounts' => $v['current_total_discounts'] ?? 0,
                        'current_total_discounts_set' => json_encode($v['current_total_discounts_set'] ?? '') ?? '',
                        'current_total_duties_set' => json_encode($v['current_total_duties_set'] ?? '') ?? '',
                        'current_total_price' => $v['current_total_price'] ?? 0,
                        'current_total_price_set' => json_encode($v['current_total_price_set'] ?? '') ?? '',
                        'current_total_tax' => $v['current_total_tax'] ?? 0,
                        'current_total_tax_set' => json_encode($v['current_total_tax_set'] ?? '') ?? '',
                        'customer_locale' => $v['customer_locale'] ?? '',
                        'device_id' => $v['device_id'] ?? '',
                        'discount_codes' => json_encode($v['discount_codes'] ?? '') ?? '',
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
                        'note_attributes' => json_encode($v['note_attributes'] ?? '') ?? '',
                        'number' => $v['number'] ?? 0,
                        'order_number' => $v['order_number'] ?? 0,
                        'order_status_url' => $v['order_status_url'] ?? '',
                        'original_total_duties_set' => json_encode($v['original_total_duties_set'] ?? '') ?? '',
                        'payment_gateway_names' => json_encode($v['payment_gateway_names'] ?? '') ?? '',
                        'phone' => $v['phone'] ?? '',
                        'presentment_currency' => $v['presentment_currency'] ?? '',
                        'processed_at' => $v['processed_at'] ?? '1000-01-01',
                        'processing_method' => $v['processing_method'] ?? '',
                        'reference' => $v['reference'] ?? '',
                        'source_identifier' => $v['source_identifier'] ?? '',
                        'source_name' => $v['source_name'] ?? '',
                        'source_url' => $v['source_url'] ?? '',
                        'subtotal_price' => $v['subtotal_price'] ?? 0,
                        'subtotal_price_set' => json_encode($v['subtotal_price_set'] ?? '') ?? '',
                        'tags' => $v['tags'] ?? '',
                        'tax_lines' => json_encode($v['tax_lines'] ?? '') ?? '',
                        'taxes_included' => $v['taxes_included'] ?? '',
                        'test' => $v['test'] ?? '',
                        'token' => $v['token'] ?? '',
                        'total_discounts' => $v['total_discounts'] ?? 0,
                        'total_discounts_set' => json_encode($v['total_discounts_set'] ?? '') ?? '',
                        'total_line_items_price' => $v['total_line_items_price'] ?? 0,
                        'total_line_items_price_set' => json_encode($v['total_line_items_price_set'] ?? '') ?? '',
                        'total_outstanding' => $v['total_outstanding'] ?? 0,
                        'total_price' => $v['total_price'] ?? 0,
                        'total_price_set' => json_encode($v['total_price_set'] ?? '') ?? '',
                        'total_price_usd' => $v['total_price_usd'] ?? 0,
                        'total_shipping_price_set' => json_encode($v['total_shipping_price_set'] ?? '') ?? '',
                        'total_tax' => $v['total_tax'] ?? 0,
                        'total_tax_set' => json_encode($v['total_tax_set'] ?? '') ?? '',
                        'total_tip_received' => $v['total_tip_received'] ?? 0,
                        'total_weight' => $v['total_weight'] ?? 0,
                        'shopify_updated_at' => $v['shopify_updated_at'] ?? '1000-01-01',
                        'user_id' => $v['user_id'] ?? '',
                        'customer' => json_encode($v['customer'] ?? '') ?? '',
                        'discount_applications' => json_encode($v['discount_applications'] ?? '') ?? '',
                        'fulfillments' => json_encode($v['fulfillments'] ?? '') ?? '',
                        'line_items' => json_encode($v['line_items'] ?? '') ?? '',
                        'payment_terms' => $v['payment_terms'] ?? '',
                        'refunds' => json_encode($v['refunds'] ?? '') ?? '',
                        'shipping_address' => json_encode($v['shipping_address'] ?? '') ?? '',
                        'shipping_lines' => json_encode($v['shipping_lines'] ?? '') ?? '',
                    ]);

                    foreach ($v['line_items'] as $li) {
                        $li['shopify_id'] = $li['id'] ?? 0;
                        $li['shopify_created_at'] = $li['created_at'] ?? '1000-01-01';
                        $li['shopify_updated_at'] = $li['updated_at'] ?? '1000-01-01';

                        OrderLineItems::updateOrCreate([
                            'store' => $store ?? '',
                            'shopify_id' => $li['shopify_id'] ?? 0,
                        ], [
                            'order_id' => $v['id'] ?? 0, // Id => Order ID
                            'product_id' => $li['product_id'] ?? 0,
                            'variant_id' => $li['variant_id'] ?? 0,
                            'admin_graphql_api_id' => $li['admin_graphql_api_id'] ?? '',
                            'fulfillable_quantity' => $li['fulfillable_quantity'] ?? 0,
                            'fulfillment_service' => $li['fulfillment_service'] ?? '',
                            'fulfillment_status' => $li['fulfillment_status'] ?? '',
                            'gift_card' => $li['gift_card'] ?? 0,
                            'grams' => $li['grams'] ?? 0,
                            'name' => $li['name'] ?? '',
                            'price' => $li['price'] ?? 0,
                            'price_set' => (json_encode($li['price_set']) ?? '') ?? '',
                            'product_exists' => $li['product_exists'] ?? 0,
                            'properties' => (json_encode($li['properties']) ?? '') ?? '',
                            'quantity' => $li['quantity'] ?? 0,
                            'requires_shipping' => $li['requires_shipping'] ?? 0,
                            'sku' => $li['sku'] ?? '',
                            'taxable' => $li['taxable'] ?? 0,
                            'title' => $li['title'] ?? '',
                            'total_discount' => $li['total_discount'] ?? 0,
                            'total_discount_set' => (json_encode($li['total_discount_set']) ?? '') ?? '',
                            'variant_inventory_management' => $li['variant_inventory_management'] ?? '',
                            'variant_title' => $li['variant_title'] ?? '',
                            'vendor' => $li['vendor'] ?? '',
                            'tax_lines' => (json_encode($li['tax_lines']) ?? '') ?? '',
                            'duties' => (json_encode($li['duties']) ?? '') ?? '',
                            'discount_allocations' => (json_encode($li['discount_allocations']) ?? '') ?? '',
                        ]);
                    }
                }
            }

        }

        $this->info("Cron Job Update Orders DONE at ". now());
    }
}
