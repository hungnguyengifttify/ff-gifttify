<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Products;
use App\Models\ProductVariants;
use App\Models\ImportProductsCsv;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Signifly\Shopify\Shopify;
use Illuminate\Support\Facades\DB;
use App\Services\RemixApi;
use App\Models\Dashboard;
use Illuminate\Support\Facades\Http;

class ImportProductsToRemix extends Command
{
    /**
     * The name and signature of the console command.
     * time ['all', 'today']
     * @var string
     */
    protected $signature = 'products_csv:import {store?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push Queue Image to S3';

    public $productType = array();

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Cron Job Push Remix Products running at ". now());

        $store = $this->argument('store') ?? '';
        $limit = 2000;

        $this->productType = DB::table('product_type')->get()->keyBy('product_type_code')->toArray();
        if ($store) {
            $products = DB::table('import_products_csv')
                ->select("*")
                ->where('syncedStatus', '=', 0)
                ->where('store', '=', $store)
                //->where('id', '=', 3)
                ->orderBy('id', 'asc')
                ->limit($limit)
                ->get();
        } else {
            $products = DB::table('import_products_csv')
                ->select("*")
                ->where('syncedStatus', '=', 0)
                //->where('id', '=', 3)
                ->orderBy('id', 'asc')
                ->limit($limit)
                ->get();
        }

        foreach ($products as $p) {
            ImportProductsCsv::where('id',$p->id)->update(['syncedStatus'=>1]);
        }

        foreach ($products as $p) {
            $this->pushProductToRemix($p);
        }

        $this->info("Cron Job Push Remix Products DONE at ". now());
    }

    public function pushImagesToS3 ($p) {
        $id = $p->id;
        $images = json_decode($p->images);

        $phpTimeZone = 'Asia/Ho_Chi_Minh';
        $today = Carbon::now($phpTimeZone);

        $newImages = array();
        foreach ($images as $k => $img) {
            $file = $img->src . '&export=download';
            $fileName = $today->format('Y/m/d') . '/' . $id . '/' . ($k+1) . '.jpg';

            $uploadDir = 'images/';
            $fullpath = $uploadDir . $fileName;

            $res = false;
            try {
                /*$response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36'
                ])->withOptions([
                    //'debug' => true,
                    'allow_redirects' => true,
                    'stream' => true
                ])->get( $file );
                $res = \Storage::disk('s3')->put($fullpath, $response->getBody()->getContents(), 'public');*/

                $res = \Storage::disk('s3')->put($fullpath, file_get_contents($file), 'public');
            } catch (\Exception $e) {
                dump($e->getMessage());
                ImportProductsCsv::where('id',$p->id)->update([
                    'syncedImage' => -1,
                    'syncedStatus'=> -1
                ]);
                return false;
            }

            if ($res) {
                $url = Config::get('filesystems.disks.s3.url') . "/" . Config::get('filesystems.disks.s3.bucket') . "/{$fullpath}";
                $newImages[] = array(
                    'src' => $url,
                    'alt' => '',
                );
            }
        }

        $variants = json_decode($p->variants);
        foreach ($variants as $k => $variant) {
            if (!empty($newImages[0]['src'])) {
                $variant->image->src = $newImages[0]['src'];
                $variants[$k] = $variant;
            }
        }

        if (!empty($newImages)) {
            ImportProductsCsv::where('id',$p->id)->update([
                's3Images'=>json_encode($newImages),
                'variants' => json_encode($variants),
                'syncedImage' => 2
            ]);
            $p->images = json_encode($newImages);
            $p->variants = json_encode($variants);
        } else {
            ImportProductsCsv::where('id',$p->id)->update([
                'syncedImage' => -1
            ]);
        }
        return $p;
    }

    public function pushProductToRemix ($p) {
        if (!$p->productType) {
            $this->info($p->shopifyId . ' ProductType is empty');
            ImportProductsCsv::where('id',$p->id)->update(['syncedStatus'=>-1]);
            return false;
        }

        if (empty(json_decode($p->variants))) {
            $this->info($p->shopifyId . ' Variants is empty');
            ImportProductsCsv::where('id',$p->id)->update(['syncedStatus'=>-1]);
            return false;
        }

        $pTypeReplaced = $this->productType[$p->productType]->product_type_name ?? '';
        if (str_contains($p->tags, 'des-') == false && $pTypeReplaced != '') {
            $newTag = trim(str_replace($pTypeReplaced,'', $p->title));
            $newTag = 'des-' . Str::slug($newTag) . '-' . date('dmy');
            $p->tags = $p->tags . ', ' . $newTag;
            $p->tagsArr = json_encode(array_map('trim', explode(',', $p->tags)));
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
        if ($p->productType == 'PRO') {
            $body['description'] = $p->description;
        }

        $query = '';
        if ($p->store != 'thecreattify.co') {
            $query = "?db=" . ImportProductsCsv::$storeDb[$p->store];
        }

        $remixApi = new RemixApi();
        $response = $remixApi->request('POST', 'products/variable' . $query, null, $body);
        if ($response && ($response->getStatusCode() == '201' || $response->getStatusCode() == '200')) {
            $res = $response->getBody()->getContents();
            $res = json_decode($res);

            $this->info($res->message . " - Store {$p->store}");

            $resApi = array();
            preg_match("/Variable product '(\w+)' created./", $res->message, $resApi);
            $returnedId = $resApi[1] ?? $p->returnedId;
            ImportProductsCsv::where('id',$p->id)->update(['syncedStatus'=>2, 'returnedId' => $returnedId]);
        } else {
            dump($body);
            $this->error('Can not created/updated');
            ImportProductsCsv::where('id',$p->id)->update(['syncedStatus'=>-2]);
        }
    }
}
