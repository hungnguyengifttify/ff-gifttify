<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MailChimpService;

class UploadOrderToMailChimp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailchimp:push_order';

    protected $json_order = '[{
        "id": "c3cotpsUd",
        "email": "michaelargent887@gmail.com",
        "cartId": "o5uLwFHD2Fx",
        "consentsAdvertising": true,
        "address": {
            "phone": "+61418471581",
            "state": "VIC",
            "country": "AU",
            "firstName": "Michael",
            "lastName": "Argent",
            "address1": "11 Bendigo avenue elwood",
            "city": "Melbourne",
            "postcode": "3184"
        },
        "paymentMethod": "paypal",
        "status": "completed",
        "createdAt": 1666846770014,
        "updatedAt": 1666847438128,
        "billingAddress": {},
        "sameBillingAddress": true,
        "items": [
            {
                "id": "IudD3cVaDn",
                "product": {
                    "id": "ZYoqBKd9W5",
                    "type": "variable",
                    "author": "admin",
                    "slug": "sheep-floral-pattern-casual-shirt",
                    "shopifyId": "g_634fa3523a7937.805474708258",
                    "title": "Sheep Floral Pattern Casual Shirt",
                    "productType": "CS",
                    "status": "publish",
                    "tags": "Casual Shirt, uni-TR2491, des-sheep-floral-pattern-191022",
                    "tagsArr": [
                        "Casual Shirt",
                        "uni-TR2491",
                        "des-sheep-floral-pattern-191022"
                    ],
                    "images": [
                        {
                            "alt": "",
                            "src": "https://cdn.shopify.com/s/files/1/0516/6730/4607/products/Mockms.jpg?v=1662433284"
                        },
                        {
                            "alt": "",
                            "src": "https://cdn.shopify.com/s/files/1/0516/6730/4607/products/14_5fcf2ca3-6fb0-4876-9a4d-749b704f5b4f.jpg?v=1662432103"
                        }
                    ],
                    "seo": {
                        "title": "Sheep Floral Pattern Casual Shirt",
                        "description": "Sheep Floral Pattern Casual Shirt"
                    },
                    "category": "Clothing",
                    "gender": "women",
                    "createdAt": 1666163542154,
                    "updatedAt": 1666163542154
                },
                "variant": {
                    "id": "gv_634fa3523a7a09.307022406173",
                    "sku": "220405-i102-03-FARM-CS-14-DES1157",
                    "image": {
                        "alt": "",
                        "src": ""
                    },
                    "price": 34.95,
                    "option1": "Casual Shirt",
                    "option2": "",
                    "option3": "",
                    "quantity": 9999,
                    "fulfilment": "manual"
                },
                "options": {
                    "Size Casual Shirt": "S"
                },
                "viewOptions": [
                    {
                        "name": "Type",
                        "value": "Casual Shirt"
                    },
                    {
                        "name": "Size Casual Shirt",
                        "value": "S"
                    }
                ],
                "price": 34.95,
                "delivery": [],
                "quantity": 2,
                "subTotal": 69.9
            }
        ],
        "cartTotal": 66.4,
        "shippingTotal": 7.99,
        "discount": 3.5,
        "couponCode": "",
        "total": 74.39,
        "currency": {
            "code": "USD",
            "symbol": "US$",
            "rate": 1
        },
        "shippingMethod": "Standard Shipping",
        "sandbox": false,
        "paypalOrderId": "0PN267367D687992Y",
        "paidAt": 1666847437672,
        "expressShipping": false,
        "transactionId": "7F2585833G861140A"
    }]';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push order to mailchimp';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $mailchimp = new MailChimpService();
        $products = json_decode($this->json_product, true);
        foreach($products as $productInfo){
            $variantsInfo = $this->getVariantsInfo($products);
            $importProduct = $mailchimp->addProduct("store_k6gosw5gwhooezt3i61m", 
            [
                "id" => $productInfo['id'],  // REQUIRE
                "title" => $productInfo['title'], // REQUIRE
                "variants" => $variantsInfo['variantsItems'], // REQUIRE
                "handle" =>  "API_PUSH", //The handle of a product.
                "url" => $productInfo['slug'], 
                "description" =>$productInfo['slug'], 
                "type" => $productInfo['productType'],
                // "vendor" // Chưa có
                "image_url" => $productInfo['images'][0]['src'] ?? '',
                // "images" => [
                //     "variant_ids" => $variantsInfo['listVariantId'] // Danh sách ID nhân ảnh
                // ], 
                "published_at_foreign" =>  date('Y-m-d h:i:s',$productInfo['createdAt']), //The date and time the product was published.
            ]);
            }
        dd($importProduct);
    }

    public function getVariantsInfo($product){
        $variantsItems = [];
        $listVariantId = [];
        if(isset($product['variants']) && count($product['variants'])){
            foreach($product['variants'] as $val){
               $vItem = [];
               $vItem['id'] = $val['id'];           
               $vItem['title'] = $product['title'];  // Không có tên cho từng option   
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
            'listVariantId'=> $listVariantId
        ];
    }
}