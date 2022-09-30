<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Products;
use App\Models\ProductVariants;
use App\Models\ImportProductsCsv;
use Illuminate\Support\Facades\Config;
use Signifly\Shopify\Shopify;
use Illuminate\Support\Facades\DB;
use App\Services\RemixApi;
use App\Models\Dashboard;
use Illuminate\Support\Facades\Http;

class DeleteProductsToRemix extends Command
{
    /**
     * The name and signature of the console command.
     * time ['all', 'today']
     * @var string
     */
    protected $signature = 'products_csv:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Products';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job Push Remix Products running at ". now());

        $limit = 10000;
        $products = DB::table('import_products_csv')
            ->select("*")
            ->where('syncedStatus', '=', -99)
            ->whereNotNull('returnedId')
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();

        /*$products = DB::select("
            select *
            from import_products_csv i
            where syncedStatus = 2 and returnedId is not null and json_length(variants)=0
            ;"
        );*/

        foreach ($products as $p) {
            ImportProductsCsv::where('id',$p->id)->update(['syncedStatus'=>-98]);
        }

        foreach ($products as $p) {
            $this->deleteProductInRemix($p);
        }

        $this->info("Cron Job Push Remix Products DONE at ". now());
    }

    public function deleteProductInRemix ($p) {
        $id = $p->id;
        $ids = array();
        $ids[] = $p->returnedId;

        $remixApi = new RemixApi();
        $body = array(
            'ids' => $ids
        );
        $response = $remixApi->request('PUT', "products/variable/batch/delete", null, $body);
        if ($response && ($response->getStatusCode() == '201' || $response->getStatusCode() == '200')) {
            $res = $response->getBody()->getContents();
            $res = json_decode($res);

            $this->info($res->message . " {$p->returnedId} - {$p->id}");

            ImportProductsCsv::where('id',$p->id)->delete();
        } else {
            dump($id);
            $this->error('Can not deleted');
            ImportProductsCsv::where('id',$p->id)->update(['syncedStatus'=>-97]);
        }
    }
}
