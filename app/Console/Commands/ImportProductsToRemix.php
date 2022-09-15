<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Products;
use App\Models\ProductVariants;
use App\Models\ImportProductsCsv;
use Illuminate\Support\Facades\Config;
use Signifly\Shopify\Shopify;
use Illuminate\Support\Facades\DB;
use App\Services\RemixApi;
use App\Models\Dashboard;

class ImportProductsToRemix extends Command
{
    /**
     * The name and signature of the console command.
     * time ['all', 'today']
     * @var string
     */
    protected $signature = 'products_csv:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Remix Products';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job Push Remix Products running at ". now());

        $limit = 1000;
        DB::table('import_products_csv')
            ->select("*")
            ->where('syncedStatus', '=', 0)
            ->where('id', '=', 3)
            ->orderBy('id', 'asc')
            ->chunk($limit, function ($products) {

            foreach ($products as $p) {
                ImportProductsCsv::where('id',$p->id)->update(['syncedStatus'=>1]);
            }

            foreach ($products as $p) {
                $this->pushProduct($p);
            }
        });

        $this->info("Cron Job Push Remix Products DONE at ". now());
    }

    public function pushProduct ($p) {
        if (!$p->productType) {
            $this->info($p->shopifyId . ' ProductType is empty');
            return false;
        }
        $body = array(
            'shopifyId' => $p->shopifyId,
            'slug' => $p->slug,
            'title' => $p->title,
            'productType' => $p->productType,
            'status' => $p->status,
            'tags' => $p->tags,
            'tagsArr' => json_decode($p->tagsArr),
            'images' => json_decode($p->images),
            'options' => json_decode($p->options),
            'variants' => json_decode($p->variants),
            'seo' => json_decode($p->seo)
        );

        $remixApi = new RemixApi();
        $response = $remixApi->request('POST', 'products/variable', null, $body);
        if ($response && ($response->getStatusCode() == '201' || $response->getStatusCode() == '200')) {
            $res = $response->getBody()->getContents();
            $res = json_decode($res);

            $this->info($res->message);

            $resApi = array();
            preg_match("/Variable product '(\w+)' created./", $res->message, $resApi);
            $returnedId = $resApi[1] ?? '';

            ImportProductsCsv::where('id',$p->id)->update(['syncedStatus'=>2, 'returnedId' => $returnedId]);
        } else {
            dump($body);
            $this->error('Can not created/updated');
            ImportProductsCsv::where('id',$p->id)->update(['syncedStatus'=>-1]);
        }

    }
}
