<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Products;
use App\Models\ProductVariants;
use Illuminate\Support\Facades\Config;
use Signifly\Shopify\Shopify;
use Illuminate\Support\Facades\DB;
use App\Services\RemixApi;
use App\Models\Dashboard;

class PushProductsToRemix extends Command
{
    /**
     * The name and signature of the console command.
     * time ['all', 'today']
     * @var string
     */
    protected $signature = 'products:remix {time_report?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Remix Products';

    public $fixProductTypeArr = array(
        '3DS' => 'T3DS',
        'HSAHSO' => 'HS',
        'POLOAHSO' => 'POLO',
    );

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job Push Remix Products running at ". now());

        $timeReport = $this->argument('time_report') ?? '';
        $limit = 2;
        if ($timeReport == 'all') {
            $limit = 1000;
        }

        $stores = array('thecreattify');
        foreach ($stores as $store) {
            DB::table('products')
                ->select(DB::raw("*, (select product_type_code from shopify_product_type where product_type_name=products.product_type LIMIT 1) as productType"))
                ->where('store', $store)
                ->where('status', 'active')
                ->where('id', '>', 14434)
                ->where('variants', 'not like', '"gid://shopify/Product/%')
                ->orderBy('id', 'asc')
                ->chunk($limit, function ($products) {

                foreach ($products as $p) {
                    if ( isset($this->fixProductTypeArr[$p->productType]) ) {
                        $p->productType = $this->fixProductTypeArr[$p->productType];
                    }
                    $images = json_decode($p->images);
                    $image = json_decode($p->image);

                    $imagesArr = array();
                    foreach ($images as $img) {
                        $imagesArr[] = array(
                            'src' => $img->src,
                            'alt' => $img->alt,
                        );
                    }

                    $options = json_decode($p->options);

                    $optionsArr = array();
                    foreach ($options as $opt) {
                        $optionsArr[] = array(
                            'name' => $opt->name,
                            'type' => '',
                            'values' => $opt->values,
                        );
                    }

                    $variants = json_decode($p->variants);

                    $var_arr = array();
                    foreach ($variants as $var) {
                        $src = $image->src ?? '';
                        $alt = $image->alt ?? '';
                        $position = $image->position ?? 0;
                        if ($var->image_id != '') {
                            foreach ($images as $img) {
                                if ($var->image_id == $img->id) {
                                    $src = $img->src;
                                    $alt = $img->alt;
                                    $position = $img->position;
                                    break;
                                }
                            }
                        }

                        $var_arr[] = array(
                            'sku' => $var->sku,
                            'quantity' => 9999,
                            'price' => $var->price,
                            'compareAtPrice' => $var->compare_at_price,
                            'option1' => $var->option1,
                            'option2' => $var->option2,
                            'option3' => $var->option3,
                            'image' => array(
                                'src' => $src,
                                'alt' => $alt,
                                'position' => $position
                            ),
                            'fulfilment' => $var->fulfillment_service
                        );
                    }

                    $body = array(
                        'slug' => $p->handle,
                        'title' => $p->title,
                        'productType' => $p->productType,
                        'status' => 'publish',
                        'tags' => $p->tags,
                        'images' => $imagesArr,
                        'options' => $optionsArr,
                        'variants' => $var_arr,
                        'seo' => array(
                            'title' => $p->title,
                            'description' => $p->title
                        )
                    );

                    $remixApi = new RemixApi();
                    $response = $remixApi->request('POST', 'products/variable', null, $body);
                    if ($response && $response->getStatusCode() == '201') {
                        $res = $response->getBody()->getContents();
                        $res = json_decode($res);

                        $this->info($res->message);
                    } else {
                        dump($body);
                        $this->error('Can not created');
                    }
                }

                $timeReport = $this->argument('time_report') ?? '';
                if ($timeReport != 'all') {
                    return false;
                }
            });
        }

        $this->info("Cron Job Push Remix Products DONE at ". now());
    }
}
