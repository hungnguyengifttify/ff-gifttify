<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dashboard;
use App\Models\WpProducts;
use App\Models\WpProductVariants;
use Automattic\WooCommerce\Client;
use Carbon\Carbon;

class GetWpProducts extends Command
{

    public $client;
    protected $signature = 'products:update_wp_product {time_report?} {store?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Products';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $storeRequest = $this->argument('store') ?? '';
        $stores = Dashboard::getWooStoresList();
        if ($storeRequest) {
            $stores = array($storeRequest);
        }
        $this->info("Cron Job $storeRequest Update Products  running at " . now());

        foreach ($stores as $store) {
            $wooConfig = Dashboard::getWoocommerceConfig($store);
          
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
                'per_page' => '3', // Max 20 need set in Woo to get More
                // 'before' => Carbon::createFromFormat('Y-m-d', $updatedAtMin, $dateTimeZone)->toIso8601String()
            ];
            $query = $defaultQuery . "?" . http_build_query($filters);
            $products = $this->client->get($query);

            foreach ($products as $p) {
                $values = array($p);
                foreach ($values as $v) {
                    $v['wp_id'] = $v['id'] ?? 0;
                    WpProducts::updateOrCreate([
                        'store' => $store ?? '',
                        'wp_id' => $v['id'] ?? 0,
                    ], [
                        'name' => $v['name'] ?? '',
                        'slug' => $v['slug'] ?? '',
                        'permalink' => $v['permalink'] ?? '',
                        'product_type' => $v['product_type'] ?? '',
                        'date_created' => $v['date_created'] ?? '1000-01-01',
                        'date_created_gmt' => $v['date_created_gmt'] ?? '1000-01-01',
                        'date_modified' => $v['date_modified'] ?? '1000-01-01',
                        'date_modified_gmt' => $v['date_modified_gmt'] ?? '1000-01-01',
                        'type' => $v['type'] ?? '',
                        'status' => $v['status'] ?? '',
                        'featured' => $v['featured'] ?? '',
                        'catalog_visibility' => $v['catalog_visibility'] ?? '',
                        'description' => $v['description'] ?? '',
                        'short_description' => $v['short_description'] ?? '',
                        'sku' => $v['sku'] ?? '',
                        'price' => $v['price'] ?? '',
                        'regular_price' => $v['regular_price'] ?? '',
                        'sale_price' => $v['sale_price'] ?? '',
                        'date_on_sale_from' => $v['date_on_sale_from'] ?? '',
                        'date_on_sale_from_gmt' => $v['date_on_sale_from_gmt'] ?? '',
                        'date_on_sale_to' => $v['date_on_sale_to'] ?? '',
                        'date_on_sale_to_gmt' => $v['date_on_sale_to_gmt'] ?? '',
                        'price_html' => $v['price_html'] ?? '',
                        'on_sale' => $v['on_sale'] ?? '',
                        'purchasable' => $v['purchasable'] ?? '',
                        'total_sales' => $v['total_sales'] ?? '',
                        'virtual' => $v['virtual'] ?? '',
                        'downloadable' => $v['downloadable'] ?? '',
                        'downloads' => $v['downloads'] ?? '',
                        'download_limit' => $v['download_limit'] ?? '',
                        'download_expiry' => $v['download_expiry'] ?? '',
                        'external_url' => $v['external_url'] ?? '',
                        'button_text' => $v['button_text'] ?? '',
                        'tax_status' => $v['tax_status'] ?? '',
                        'tax_class' => $v['tax_class'] ?? '',
                        'manage_stock' => $v['manage_stock'] ?? '',
                        'stock_quantity' => $v['stock_quantity'] ?? '',
                        'stock_status' => $v['stock_status'] ?? '',
                        'backorders' => $v['backorders'] ?? '',
                        'backorders_allowed' => $v['backorders_allowed'] ?? '',
                        'backordered' => $v['backordered'] ?? '',
                        'sold_individually' => $v['sold_individually'] ?? '',
                        'weight' => $v['weight'] ?? '',
                        'dimensions' => json_encode($v['dimensions'] ?? '') ?? '',
                        'shipping_required' => $v['shipping_required'] ?? '',
                        'shipping_taxable' => $v['shipping_taxable'] ?? '',
                        'shipping_class' => $v['shipping_class'] ?? '',
                        'shipping_class_id' => $v['shipping_class_id'] ?? '',
                        'reviews_allowed' => $v['reviews_allowed'] ?? '',
                        'average_rating' => $v['average_rating'] ?? '',
                        'rating_count' => $v['rating_count'] ?? '',
                        'related_ids' => json_encode($v['related_ids'] ?? '') ?? '',
                        'upsell_ids' => json_encode($v['upsell_ids'] ?? '') ?? '',
                        'cross_sell_ids' => json_encode($v['cross_sell_ids'] ?? '') ?? '',
                        'parent_id' => $v['parent_id'] ?? '',
                        'purchase_note' => $v['purchase_note'] ?? '',
                        'categories' => json_encode($v['categories'] ?? '') ?? '',
                        'tags' => json_encode($v['tags'] ?? '') ?? '',
                        'images' => json_encode($v['images'] ?? '') ?? '',
                        'attributes' => json_encode($v['attributes'] ?? '') ?? '',
                        'default_attributes' => json_encode($v['default_attributes'] ?? '') ?? '',
                        'variations' => json_encode($v['variations'] ?? '') ?? '',
                        'grouped_products' => json_encode($v['grouped_products'] ?? '') ?? '',
                        'menu_order' => $v['menu_order'] ?? '',
                        'meta_data' => json_encode($v['meta_data'] ?? '') ?? '',
                        '_links' => json_encode($v['_links'] ?? '') ?? '',
                    ]);

                    if (!empty($v['variations'])) {
                        $this->updateVariationsProduct($v['wp_id']);
                    }
                }
            }
        }
        $this->info("Cron Job Update Products DONE at " . now());
    }

    public function updateVariationsProduct($wpId)
    {
        $defaultQuery = 'products' . '/' . $wpId . '/' . 'variations';
        $filters = [
            'per_page' => '3', // Max 20 need set in Woo to get More
            // 'before' => Carbon::createFromFormat('Y-m-d', $updatedAtMin, $dateTimeZone)->toIso8601String()
        ];
        $query = $defaultQuery . "?" . http_build_query($filters);
        $variantProducts = $this->client->get($query);

        foreach ($variantProducts as $variant) {
            $variant = array($variant);
            $variant['shopify_id'] = $variant['id'] ?? 0;
            // $variant['shopify_created_at'] = $variant['created_at'] ?? '1000-01-01';
            // $variant['shopify_updated_at'] = $variant['updated_at'] ?? '1000-01-01';

            WpProductVariants::updateOrCreate([
                'store' => $store ?? '',
                'shopify_id' => $variant['shopify_id'] ?? 0,
            ], [
                'product_id' => $variant['product_id'] ?? 0,
                'shopify_created_at' => $variant['shopify_created_at'] ?? '1900-01-01',
                'shopify_updated_at' => $variant['shopify_updated_at'] ?? '1900-01-01',
                'barcode' => $variant['barcode'] ?? '',
                'compare_at_price' => $variant['compare_at_price'] ?? 0,
                'fulfillment_service' => $variant['fulfillment_service'] ?? '',
                'grams' => $variant['grams'] ?? 0,
                'weight' => $variant['weight'] ?? 0,
                'weight_unit' => $variant['weight_unit'] ?? '',
                'inventory_item_id' => $variant['inventory_item_id'] ?? 0,
                'inventory_management' => $variant['inventory_management'] ?? '',
                'inventory_policy' => $variant['inventory_policy'] ?? '',
                'inventory_quantity' => $variant['inventory_quantity'] ?? 0,
                'option1' => $variant['option1'] ?? '',
                'option2' => $variant['option2'] ?? '',
                'option3' => $variant['option3'] ?? '',
                'position' => $variant['position'] ?? 0,
                'price' => $variant['price'] ?? 0,
                'requires_shipping' => $variant['requires_shipping'] ?? 0,
                'sku' => $variant['sku'] ?? '',
                'taxable' => $variant['taxable'] ?? 0,
                'title' => $variant['title'] ?? '',
            ]);
        }
    }
}
