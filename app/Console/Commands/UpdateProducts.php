<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Products;
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

        $stores = array('us', 'au');

        foreach ($stores as $store) {
            if ($store == 'us') {
                $apiKey = env('SHOPIFY_US_API_KEY', '');
                $password = env('SHOPIFY_US_PASSWORD', '');
                $domain = env('SHOPIFY_US_DOMAIN', '');
                $apiVersion = env('SHOPIFY_US_API_VERSION', '');

                $dateTimeZone = new \DateTimeZone('America/Los_Angeles');
            } elseif ($store == 'au') {
                $apiKey = env('SHOPIFY_AU_API_KEY', '');
                $password = env('SHOPIFY_AU_PASSWORD', '');
                $domain = env('SHOPIFY_AU_DOMAIN', '');
                $apiVersion = env('SHOPIFY_AU_API_VERSION', '');

                $dateTimeZone = new \DateTimeZone('Australia/Sydney');
            }

            $dateTime = new \DateTime("now", $dateTimeZone);
            $dateTime->modify('-1 day');
            $createdAtMin = $dateTime->format('Y-m-d');

            $timeReport = $this->argument('time_report') ?? '';
            if ($timeReport == 'all') {
                $createdAtMin = '1900-01-01';
            }

            $shopify = new Shopify($apiKey, $password, $domain, $apiVersion);
            $products = $shopify->paginateProducts([
                'created_at_min' => $createdAtMin,
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
                        'variants' => json_encode($v['admin_graphql_api_id'] ?? '') ?? '',
                        'options' => json_encode($v['options'] ?? '') ?? '',
                        'images' => json_encode($v['images'] ?? '') ?? '',
                        'image' => json_encode($v['image'] ?? '') ?? '',
                    ]);

                }
            }
        }
        $this->info("Cron Job Update Products DONE at ". now());
    }
}
