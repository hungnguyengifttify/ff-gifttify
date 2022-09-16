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
    protected $description = 'Push Queue Image to S3';

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
            ->where('syncedStatus', '=', 0)
            //->where('id', '=', 3)
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();

        foreach ($products as $p) {
            ImportProductsCsv::where('id',$p->id)->update(['syncedStatus'=>1]);
        }

        foreach ($products as $p) {
            $p = $this->pushImagesToS3($p);
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
            $file = $img->src;
            $fileName = $today->format('Y/m/d') . '/' . $id . '/' . ($k+1) . '.jpg';

            $uploadDir = 'images/';
            $fullpath = $uploadDir . $fileName;
            $res = \Storage::disk('s3')->put($fullpath, file_get_contents($file), 'public');
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
            $returnedId = $resApi[1] ?? $p->returnedId;

            ImportProductsCsv::where('id',$p->id)->update(['syncedStatus'=>2, 'returnedId' => $returnedId]);
        } else {
            dump($body);
            $this->error('Can not created/updated');
            ImportProductsCsv::where('id',$p->id)->update(['syncedStatus'=>-2]);
        }
    }
}
