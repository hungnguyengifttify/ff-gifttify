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

class RemixScheduleProduct extends Command
{
    /**
     * The name and signature of the console command.
     * time ['all', 'today']
     * @var string
     */
    protected $signature = 'remix:scheduleproduct';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remix Schedule Product';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job Remix Schedule Product running at ". now());

        $arrDb = array(1,2,3,4,5,6);
        foreach ($arrDb as $db) {
            $remixApi = new RemixApi();
            $response = $remixApi->request('PUT', "products/bulk-editor/schedule?db=$db", null, null);
            if ($response && $response->getStatusCode() == '200') {
                $res = $response->getBody()->getContents();
                $res = json_decode($res);

                $this->info($res->message ?? 'Done');
            } else {
                dump('Err');
                //$this->error('Something wrong');
            }
        }

        $this->info("Cron Job Remix Schedule Product DONE at ". now());
    }
}
