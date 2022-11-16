<?php

namespace App\Console\Commands;

use App\Models\ImportProductsCsv;
use App\Services\RemixApi;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\AdAccount;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\FbAds;
use App\Models\FbCampaigns;
use FacebookAds\Object\Fields\AdAccountFields;
use App\Models\RedisGtf;

class TestCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cmd {time_report?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Google Drive files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job running at ". now());

        $orders = RedisGtf::getRedisOrdersList(1, array('2022-11-14', '2022-11-14'), 'completed');
        //dump($orders);

        //$products = RedisGtf::getRedisProductsList(1, 1, 10);
        //dump($products);

        //https://thecreattify.com/products/tractor-pattern-casual-shirt-PN336nVbyr
        $product = RedisGtf::getRedisProductDetail($db = 1, 'PN336nVbyr');
        dump($product);

        $this->info("Cron Job end at ". now());
        $this->info('Success!');
    }

}
