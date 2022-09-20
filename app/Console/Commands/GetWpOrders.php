<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dashboard;
use App\Models\Orders;
use App\Models\OrderLineItems;
use Automattic\WooCommerce\Client;

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
        $this->info("Cron Job Update Wordpress Orders running at " . now());
        $stores = Dashboard::getWooStoresList();

        foreach ($stores as $store) {
            $wooConfig = Dashboard::getWooStoresList($store);

            $domain = $wooConfig['domain'];
            $consumerKey = $wooConfig['consumerKey'];
            $consumerSecret = $wooConfig['consumerSecret'];
            $apiVersion = $wooConfig['apiVersion'];
            $dateTimeZone = $wooConfig['dateTimeZone'];

            $dateTime = new \DateTime("now", $dateTimeZone);
            $dateTime->modify('-1 day');
            $updatedAtMin = $dateTime->format('Y-m-d');

            $timeReport = $this->argument('time_report') ?? '';
            if ($timeReport == 'all') {
                $updatedAtMin = '1900-01-01';
            }

            $this->client = new Client(
                $domain,
                $consumerKey,
                $consumerSecret,
                [
                    'wp_api' => true,
                    'version' => $apiVersion
                ]
            );

            $defaultQuery = 'products';
            $filters = [
                'per_page' => '3' //Todo test params
            ];
            $query = $defaultQuery . "?" . http_build_query($filters);
            $orders = $this->client->get($query);


            foreach ($orders as $o) {
                $values = array($p);
                foreach ($values as $v) {
                    $v['shopify_id'] = $v['id'] ?? 0;
                    Orders::updateOrCreate([
                        'store' => $store ?? '',
                        'wp_id' => $v['wp_id'] ?? 0,
                    ], [
                        'wp_id' => $v['wp_id'] ?? 0,
                        'parent_id' => $v['parent_id'] ?? 0,
                        'number' => $v['number'] ?? '',
                        'order_key' => $v['order_key'] ?? '',
                        'created_via' => $v['created_via'] ?? '',
                        'version' => $v['version'] ?? '',
                        'status' => $v['status'] ?? '',
                        'currency' => $v['currency'] ?? '',
                        'date_created' =>  $v['date_created'] ?? '1000-01-01',
                        'date_created_gmt' =>  $v['date_created_gmt'] ?? '1000-01-01',
                        'date_modified' =>  $v['date_modified'] ?? '1000-01-01',
                        'date_modified_gmt' =>  $v['date_modified_gmt'] ?? '1000-01-01',
                        'discount_total' => $v['discount_total'] ?? 0,
                        'discount_tax' => $v['discount_tax'] ?? 0,
                        'shipping_total' => $v['shipping_total'] ?? 0,
                        'shipping_tax' => $v['shipping_tax'] ?? 0,
                        'cart_tax' => $v['cart_tax'] ?? 0,
                        'total' => $v['total'] ?? 0,
                        'total_tax' => $v['total_tax'] ?? 0,
                        'prices_include_tax' => $v['prices_include_tax'] ?? 0,
                        'customer_id' => $v['customer_id'] ?? 0,
                        'customer_ip_address' => $v['customer_ip_address'] ?? 0,
                        'customer_user_agent' => $v['customer_user_agent'] ?? 0,
                        'customer_note' => $v['customer_note'] ?? 0,
                        'billing' => json_encode($v['billing'] ?? '') ?? '',
                        'shipping' => json_encode($v['shipping'] ?? '') ?? '',
                        'payment_method' => $v['payment_method'] ?? '',
                        'payment_method_title' => $v['payment_method_title'] ?? '',
                        'transaction_id' => $v['transaction_id'] ?? '',
                        'date_paid' =>  $v['date_paid'] ?? '1000-01-01',
                        'date_paid_gmt' =>  $v['date_paid_gmt'] ?? '1000-01-01',
                        'date_completed' =>  $v['date_completed'] ?? '1000-01-01',
                        'date_completed_gmt' =>  $v['date_completed_gmt'] ?? '1000-01-01',
                        'cart_hash' =>  $v['cart_hash'] ?? '1000-01-01',
                        'meta_data' => json_encode($v['meta_data'] ?? '') ?? '',
                        'line_items' => json_encode($v['line_items'] ?? '') ?? '',
                        'shipping_lines' => json_encode($v['shipping_lines'] ?? '') ?? '',
                        'fee_lines' => json_encode($v['fee_lines'] ?? '') ?? '',
                        'coupon_lines' => json_encode($v['coupon_lines'] ?? '') ?? '',
                        'refunds' => json_encode($v['refunds'] ?? '') ?? '',
                        '_links' => json_encode($v['_links'] ?? '') ?? '',
                    ]);

                    foreach ($v['line_items'] as $li) {
                        $li['wp_id'] = $li['id'] ?? 0;
                        // $li['shopify_created_at'] = $li['created_at'] ?? '1000-01-01';
                        // $li['shopify_updated_at'] = $li['updated_at'] ?? '1000-01-01';

                        OrderLineItems::updateOrCreate([
                            'store' => $store ?? '',
                            'wp_id' => $li['wp_id'] ?? 0,
                        ], [
                            'order_id' => $v['id'] ?? 0, // Id => Order ID
                            'product_id' => $li['product_id'] ?? 0,
                            'store' => $v['store'] ?? 0,
                            'wp_id' => $v['wp_id'] ?? 0,
                            'name' => $v['name'] ?? 0,
                            'product_id' => $v['product_id'] ?? 0,
                            'variation_id' => $v['variation_id'] ?? 0,
                            'quantity' => $v['quantity'] ?? 0,
                            'tax_class' => $v['tax_class'] ?? 0,
                            'subtotal' => $v['subtotal'] ?? 0,
                            'subtotal_tax' => $v['subtotal_tax'] ?? 0,
                            'total' => $v['total'] ?? 0,
                            'total_tax' => $v['total_tax'] ?? 0,
                            'taxes' =>  json_encode($v['taxes'] ?? '') ?? '',
                            'meta_data' =>  json_encode($v['meta_data'] ?? '') ?? '',
                            'sku' => $v['sku'] ?? 0,
                            'price' => $v['price'] ?? 0,
                        ]);
                    }
                }
            }
        }

        $this->info("Cron Job Update Wordpress Orders DONE at " . now());
    }
}
