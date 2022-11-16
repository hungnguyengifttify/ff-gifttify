<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MailChimpService;

class UploadProductToMailChimp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailchimp:push_product';

    public $json_product = '[{
        "id": "gU47IVX32g",
        "type": "variable",
        "author": "admin",
        "slug": "pine-tree-christmas-tree-on-snowflake-background-duffle-bag",
        "shopifyId": "g_633a73850db2a1.018684573666",
        "title": "Pine Tree, Christmas Tree On Snowflake Background Duffle Bag",
        "productType": "DBA",
        "status": "publish",
        "tags": "uni-ID1205, xmas, Duffle Bag, des-pine-tree-christmas-tree-on-snowflake-background-111022",
        "tagsArr": [
            "uni-ID1205",
            "xmas",
            "Duffle Bag",
            "des-pine-tree-christmas-tree-on-snowflake-background-111022"
        ],
        "images": [
            {
                "alt": "",
                "src": "https://s3.amazonaws.com/thecreattify.co/images/2022_Sep/30/ChristmasID1205_49078_U/Duffle Bag/Pine Tree, Christmas Tree On Snowflake Background/MK_1.jpg"
            },
            {
                "alt": "",
                "src": "https://s3.amazonaws.com/thecreattify.co/images/2022_Sep/30/ChristmasID1205_49078_U/Duffle Bag/Pine Tree, Christmas Tree On Snowflake Background/MK_2.jpg"
            }
        ],
        "options": [
            {
                "name": "Size",
                "type": "",
                "values": [
                    "S",
                    "M",
                    "L"
                ]
            }
        ],
        "variants": [
            {
                "id": "gv_633a73850db2e2.708869766698",
                "sku": "ID1205DBA-PINETREE,CHRISTMASTREEONSNOWFLAKEBACKGROUND-S",
                "image": {
                    "alt": "",
                    "src": ""
                },
                "price": 42.95,
                "option1": "S",
                "option2": "",
                "option3": "",
                "quantity": 9999,
                "fulfilment": "manual"
            },
            {
                "id": "gv_633a73850db322.109299489463",
                "sku": "ID1205DBA-PINETREE,CHRISTMASTREEONSNOWFLAKEBACKGROUND-M",
                "image": {
                    "alt": "",
                    "src": ""
                },
                "price": 42.95,
                "option1": "M",
                "option2": "",
                "option3": "",
                "quantity": 9999,
                "fulfilment": "manual"
            },
            {
                "id": "gv_633a73850db365.254890777891",
                "sku": "ID1205DBA-PINETREE,CHRISTMASTREEONSNOWFLAKEBACKGROUND-L",
                "image": {
                    "alt": "",
                    "src": ""
                },
                "price": 42.95,
                "option1": "L",
                "option2": "",
                "option3": "",
                "quantity": 9999,
                "fulfilment": "manual"
            }
        ],
        "seo": {
            "title": "Pine Tree, Christmas Tree On Snowflake Background Duffle Bag",
            "description": "Pine Tree, Christmas Tree On Snowflake Background Duffle Bag"
        },
        "category": "Handbag & Wallet Accessories",
        "gender": "unisex",
        "createdAt": 1664775081763,
        "updatedAt": 1664775081763,
        "lastEditAuthor": "admin"
    }]';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push product to mailchimp';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $mailchimp = new MailChimpService();
        $products = json_decode($this->json_product, true);

        //$import = $mailchimp->getStoreProduct("store_k6gosw5gwhooezt3i61m", $productInfo['id']) ;

        foreach ($products as $productInfo) {
            $variantsInfo = $this->getVariantsInfo($productInfo);
            // dd($variantsInfo);
            $importProduct = $mailchimp->addProduct(
                "store_k6gosw5gwhooezt3i61m",
                [
                    "id" => $productInfo['id'],  // REQUIRE
                    "title" => $productInfo['title'], // REQUIRE
                    "variants" => $variantsInfo['variantsItems'], // REQUIRE
                    "handle" =>  "API_PUSH", //The handle of a product.
                    "url" => 'https://thecreattify.com/' . $productInfo['slug'] . '-' .  $productInfo['id'],
                    "description" => "",
                    "type" => $productInfo['productType'],
                    // "vendor" // Chưa có
                    "image_url" => $productInfo['images'][0]['src'] ?? '',
                    // "images" => [
                    //     "variant_ids" => $variantsInfo['listVariantId'] // Danh sách ID nhân ảnh
                    // ], 
                    "published_at_foreign" =>  date('Y-m-d h:i:s', $productInfo['createdAt']), //The date and time the product was published.
                ]
            );
        }
    }
    public function getVariantsInfo($product)
    {
        $variantsItems = [];
        $listVariantId = [];
        if (isset($product['variants']) && count($product['variants'])) {
            foreach ($product['variants'] as $val) {
                $vItem = [];
                $vItem['id'] = $val['id'];
                $vItem['title'] = $product['title'] . '-' .  $val['id'];  // Không có tên cho từng option   
                $vItem['url'] = $product['slug'] ?? '';
                $vItem['sku'] = $val['sku'];
                $vItem['price'] = $val['price'];
                $vItem['inventory_quantity'] = $val['quantity'];
                $vItem['image_url'] = $product['image'][0]['src'] ?? '';  // Dùng ảnh mặc định của sản phẩm gốc
                //    $vItem['backorders'] = $val['backorders'];   // Không có thông tin
                //    $vItem['visibility'] = $val['visibility'];   // Không có thông tin
                $variantsItems[] = $vItem;
                $listVariantId[] = $val['id'];
            }
        }

        return [
            'variantsItems' => $variantsItems,
            'listVariantId' => $listVariantId
        ];
    }
}
