<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dashboard;
use App\Models\Products;
use App\Models\ProductVariants;
use Signifly\Shopify\Shopify;

class UpdateProducts extends Command
{
    /**
     * The name and signature of the console command.
     * time ['all', 'today']
     * @var string
     */
    protected $signature = 'products:update {time_report?}';

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
        $this->info("Cron Job Update Products running at ". now());

        $stores = array('thecreattify', 'au-thecreattify', 'singlecloudy');

        foreach ($stores as $store) {
            $shopifyConfig = Dashboard::getShopifyConfig($store);

            $apiKey = $shopifyConfig['apiKey'];
            $password = $shopifyConfig['password'];
            $domain = $shopifyConfig['domain'];
            $apiVersion = $shopifyConfig['apiVersion'];
            $dateTimeZone = $shopifyConfig['dateTimeZone'];

            $dateTime = new \DateTime("now", $dateTimeZone);
            $dateTime->modify('-1 day');
            $updatedAtMin = $dateTime->format('Y-m-d');

            $timeReport = $this->argument('time_report') ?? '';
            if ($timeReport == 'all') {
                $updatedAtMin = '1900-01-01';
            }

            $shopify = new Shopify($apiKey, $password, $domain, $apiVersion);
            $products = $shopify->paginateProducts([
                'updated_at_min' => $updatedAtMin,
                'limit' => 250
            ]);

            foreach ($products as $p) {
                $values = $p->toArray();
                foreach ($values as $v) {
                    $v['shopify_id'] = $v['id'] ?? 0;
                    $v['shopify_created_at'] = $v['created_at'] ?? '1000-01-01';
                    $v['shopify_updated_at'] = $v['updated_at'] ?? '1000-01-01';
                    Products::updateOrCreate([
                        'store' => $store ?? '',
                        'shopify_id' => $v['shopify_id'] ?? 0,
                    ], [
                        'title' => $v['title'] ?? '',
                        'body_html' => $v['body_html'] ?? '',
                        'vendor' => $v['vendor'] ?? '',
                        'product_type' => $v['product_type'] ?? '',
                        'shopify_created_at' => $v['shopify_created_at'] ?? '1000-01-01',
                        'handle' => $v['handle'] ?? '',
                        'shopify_updated_at' => $v['shopify_updated_at'] ?? '1000-01-01',
                        'published_at' => $v['published_at'] ?? '1000-01-01',
                        'template_suffix' => $v['template_suffix'] ?? '',
                        'status' => $v['status'] ?? '',
                        'published_scope' => $v['published_scope'] ?? '',
                        'tags' => $v['tags'] ?? '',
                        'admin_graphql_api_id' => $v['admin_graphql_api_id'] ?? '',
                        'variants' => json_encode($v['variants'] ?? '') ?? '',
                        'options' => json_encode($v['options'] ?? '') ?? '',
                        'images' => json_encode($v['images'] ?? '') ?? '',
                        'image' => json_encode($v['image'] ?? '') ?? '',
                    ]);

                    if (!empty($v['variants'])) {
                        foreach ($v['variants'] as $variant) {
                            $variant['shopify_id'] = $variant['id'] ?? 0;
                            $variant['shopify_created_at'] = $variant['created_at'] ?? '1000-01-01';
                            $variant['shopify_updated_at'] = $variant['updated_at'] ?? '1000-01-01';

                            ProductVariants::updateOrCreate([
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
            }
        }
        $this->info("Cron Job Update Products DONE at ". now());
    }
}
