<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Products;
use App\Models\ProductVariants;
use Illuminate\Support\Facades\Config;
use Prophecy\Exception\Exception;
use Signifly\Shopify\Shopify;
use Illuminate\Support\Facades\DB;
use App\Services\RemixApi;
use App\Models\Dashboard;

class PushProductTypeToRemix extends Command
{
    /**
     * The name and signature of the console command.
     * time ['all', 'today']
     * @var string
     */
    protected $signature = 'producttype:remix {time_report?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Remix Product Type';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job Push Remix Product Type running at ". now());

        $limit = 1000;
        $stores = array('thecreattify');
        foreach ($stores as $store) {
            DB::table(DB::raw("( select product_type from products where store='$store' group by product_type ) a"))
                ->select(DB::raw("a.product_type, shopify_product_type.product_type_code as id,
	(select product_type_name from product_type where product_type_code=shopify_product_type.product_type_code) as title,
	(select body_html from products where product_type=a.product_type and body_html != '' limit 1) as description,
	(select variants->>'$[0].price' from products where product_type=a.product_type and body_html != '' limit 1) as basePrice"))
                ->leftJoin('shopify_product_type', 'shopify_product_type.product_type_name', '=', 'a.product_type')
                ->where('a.product_type', '!=', 'options_price')
                ->where('a.product_type', '!=', '')
                ->orderBy('a.product_type', 'asc')
                ->chunk($limit, function ($productTypes) {

                foreach ($productTypes as $pt) {
                    $body = array(
                        'id' => $pt->id,
                        'title' => $pt->title,
                        'description' => $pt->description,
                        'basePrice' => $pt->basePrice,
                        'status' => 'publish',
                    );

                    $remixApi = new RemixApi();

                    $response = $remixApi->request('POST', 'templates', null, $body);
                    if ($response && $response->getStatusCode() == '201') {
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

        $this->info("Cron Job Push Remix Product Type DONE at ". now());
    }
}
