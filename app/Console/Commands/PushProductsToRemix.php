<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Products;
use App\Models\ProductVariants;
use Illuminate\Support\Facades\Config;
use Signifly\Shopify\Shopify;
use Illuminate\Support\Facades\DB;
use App\Services\RemixApi;

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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job Push Remix Products running at ". now());

        $timeReport = $this->argument('time_report') ?? '';
        $limit = 1000;
        if ($timeReport == 'all') {
            $limit = 1000;
        }

        $stores = array('us');
        foreach ($stores as $store) {
            DB::table('products')->where('store', $store)->orderBy('id', 'asc')->chunk($limit, function ($products) {
                foreach ($products as $p) {
                    $images = json_decode($p->images);
                    $image = json_decode($p->image);

                    $options = json_decode($p->options);
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
                            'image' => array(
                                'src' => $src,
                                'alt' => $alt,
                                'position' => $position
                            ),
                        );
                    }

                    $body = array(
                        'title' => $p->title,
                        'slug' => $p->handle,
                        'productType' => $p->product_type,
                        'status' => $p->status,
                        'tags' => $p->tags,
                        'options' => array(
                            'option1' => array(
                                'name' => $options[0]->name ?? '',
                                'type' => '',
                                'value' => $options[0]->values ?? array(),
                            ),
                            'option2' => array(
                                'name' => $options[1]->name ?? '',
                                'type' => '',
                                'value' => $options[1]->values ?? array(),
                            ),
                            'option3' => array(
                                'name' => $options[2]->name ?? '',
                                'type' => '',
                                'value' => $options[2]->values ?? array(),
                            ),
                        ),
                        'variants' => $var_arr,
                        'seo' => array(
                            'title' => $p->title,
                            'description' => $p->title
                        )
                    );

                    $remixApi = new RemixApi();
                    $response = $remixApi->request('POST', 'products/variable', null, $body);
                    if ($response->getStatusCode() == '201') {
                        $res = $response->getBody()->getContents();
                        $res = json_decode($res);

                        $this->info($res->message);
                    } else {
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
