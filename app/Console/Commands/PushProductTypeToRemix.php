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
use App\Models\RemixProductType;

class PushProductTypeToRemix extends Command
{
    /**
     * The name and signature of the console command.
     * time ['all', 'today']
     * @var string
     */
    protected $signature = 'producttype:remix';

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
            DB::table('remix_product_type')
                //->whereIn('id', array('WOSB', 'PH', 'CWM'))
                ->orderBy('id', 'asc')
                ->chunk($limit, function ($productTypes) {

                foreach ($productTypes as $pt) {
                    $body = array(
                        'id' => $pt->id,
                        'title' => $pt->title,
                        'description' => $pt->description,
                        'basePrice' => $pt->base_price,
                        'status' => 'publish',
                        'category' => $pt->category,
                        'gender' => $pt->gender,
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
            });

        }

        $this->info("Cron Job Push Remix Product Type DONE at ". now());
    }
}
