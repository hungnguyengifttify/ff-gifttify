<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use App\Models\RedisGtf;
use Illuminate\Console\Command;
use App\Services\MailChimpService;
use Exception;

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
    }, 
    {
        "id": "c3cotpsUd_test",
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
        $siteDomain = 'https://thecreattify.com';
        $storeID = 'store_k6gosw5gwhooezt3i61m';
        $pageOrder = 0;

        $mailchimp = new MailChimpService();
        // $response = $mailchimp->service->ecommerce->stores();
        do {
            $pageOrder++;
            $data = RedisGtf::getRedisOrdersList(1, array('2022-11-01', '2022-11-16'), 'draft', $pageOrder, 100);
            foreach ($data['results'] as $v) {
                // Import order
                $listProduct = $this->getOrderItems($v);
                foreach($listProduct as $productInfo){
                    try {
                        // try {
                        $mailchimp->service->ecommerce->updateStoreProduct(
                            $storeID, 
                            $productInfo['product']["id"],
                            [
                                "title" => $productInfo['product']['title'], // REQUIRE
                                "url" => $siteDomain . '/products/' . $productInfo['product']['slug'] . '-' .  $productInfo['product']['id'],
                                "type" => $productInfo['product']['type'],
                                "image_url" => $productInfo['product']['images'][0]['src'] ?? '',
                            ]
                        );
                        // }catch (Exception $e) {}
                        $variantInfo = [
                            "id" => $productInfo['variant']['id'], // REQUIRE
                            "title" => $productInfo['product']['title'], // REQUIRE
                            "url" => $siteDomain . '/products/' . $productInfo['product']['slug'] . '-' .  $productInfo['product']['id'],
                            "sku" => $productInfo['variant']['sku'], //The handle of a product.
                            "price" => $productInfo['variant']['price'],
                            "image_url" => $productInfo['product']['images'][0]['src'] ?? '',
                            // "images" => [
                            //     "variant_ids" => $variantsInfo['listVariantId'] // Danh sách ID nhân ảnh
                            // ], 
                            "published_at_foreign" =>  date('c', $productInfo['product']['createdAt']), //The date and time the product was published.
                        ];
                        $updatePrdVr = $mailchimp->service->ecommerce->addProductVariant(
                            $storeID,
                            $productInfo['product']['id'],
                            $productInfo['variant']['id'],
                            $variantInfo
                        );

                    }catch (Exception $e) {
                        if($e->getResponse()->getStatusCode() == 404){
                            try {
                                $infoProduct = [
                                    "id" => $productInfo['product']['id'],  // REQUIRE
                                    "title" => $productInfo['product']['title'], // REQUIRE
                                    "variants" => [
                                        [
                                        'id' =>  $productInfo['variant']['id'],
                                        'title' => $productInfo['product']['title'],  
                                        'url' => $siteDomain.'products/' . $productInfo['product']['slug'] . '-' .  $productInfo['product']['id'],
                                        'sku' => $productInfo['variant']['sku'],
                                        'price' => $productInfo['variant']['price'],
                                        'inventory_quantity' => $productInfo['variant']['quantity'],                              
                                        'image_url' => $productInfo['product']['images'][0]['src'] ?? '',  
                                        ]
                                    ], // REQUIRE
                                    "handle" =>  "API_PUSH", //The handle of a product.
                                    "url" => $siteDomain.'products/' . $productInfo['product']['slug'] . '-' .  $productInfo['product']['id'],
                                    "description" => "",
                                    "type" => $productInfo['product']['productType'],
                                    "published_at_foreign" =>  date('c', $productInfo['product']['createdAt']), //The date and time the product was published.
                                ];

                                $importProduct = $mailchimp->service->ecommerce->addStoreProduct(
                                    $storeID,
                                    $infoProduct
                                );
                            }catch (Exception $e) {
                                dump($e);
                            };
                        };
                    }
                }

                // Import Customer
                $updateCustomer = $mailchimp->service->ecommerce->setStoreCustomer($storeID, $v["email"], [
                    "id" => $v["email"],
                    "email_address" => $v["email"],
                    "opt_in_status" => true,
                    "first_name" => $v["address"]["firstName"],
                    "last_name" => $v["address"]["lastName"],
                    "phone" => $v["address"]["phone"],
                     "address" => [
                        'address1' => $v["address"]["address1"] ?? '',
                        'city' => $v["address"]["city"] ?? '',
                        'postal_code' => $v["address"]["postcode"] ?? '',
                        'country_code' => $v["address"]["country"] ?? '',
                        'province' => $v["address"]["state"] ?? ''
                    ]
                ]);

                // Import order or cart
                if($v["status"] == 'draft'){
                    // Show all cart
                    // $mailchimp->service->ecommerce->getStoreCarts($storeID);
                    
                    // Import Cart
                    $dataInsert = [
                        "id" => $v["id"],
                        "customer" => [
                            "id" => $v["email"], //A unique identifier for the customer. Limited to 50 characters.
                        ], //Information about a specific customer. For existing customers include only the id parameter in the customer object body.
                        "currency_code" => $v["currency"]["code"] ?? 'USD',
                        "order_total" =>  $this->totalPriceDraft($v), // The order total for the cart.
                        "lines" => $this->getLines($v),
                        "checkout_url" => $siteDomain . 'checkout/' . $v["id"] // A string that uniquely identifies the campaign for an order.
                    ];

                    try {
                        $addOrder = $mailchimp->service->ecommerce->addStoreCart(
                            $storeID,
                            $dataInsert
                        );
        
                        if($addOrder){
                            dump('Insert cart ID: "'. $v["id"] .'" Thành công cho email: ' . $v["email"]);
                            Log::notice('Insert cart ID: "'. $v["id"] .'" Thành công cho email: ' . $v["email"]);
                        }
                    }catch (Exception $e) {
                        if($e->getResponse()->getStatusCode() == 400){
                            dump('Cart '.$v["id"]. ''. $v["email"].' tồn tại vui lòng xóa hoăc bật xóa order để cập nhâp');
                            Log::notice('Cart '.$v["id"]. ''. $v["email"].' tồn tại vui lòng xóa hoăc bật xóa order để cập nhâp');
                        }else{
                            Log::notice($e);
                        };
                    }
                }else{
                    try {
                        $mailchimp->service->ecommerce->deleteOrder($storeID, $v["id"]);
                        dump('Xóa Order '.$v["id"]. 'Email: ' . $v["email"]);
                        Log::notice(('Xóa Order '.$v["id"]. 'Email: ' . $v["email"]));
                    }catch (Exception $e) {
                    }
                    $dataInsert = [
                        "id" => $v["id"],
                        "customer" => [
                            "id" => $v["email"], //A unique identifier for the customer. Limited to 50 characters.
                        ], //Information about a specific customer. For existing customers include only the id parameter in the customer object body.
                        "currency_code" => $v["currency"]["code"],
                        "order_total" =>  $v["total"], // The total for the order.
                        "lines" => $this->getLines($v),
        
                        // "campaign_id" // A string that uniquely identifies the campaign for an order.
                        // "landing_site" // The URL for the page where the buyer landed when entering the shop.
                       
                        "financial_status" => 'paid', //paid, pending, refunded, cancelled
                        "fulfillment_status" => $v["status"],
                       
                        "discount_total" => $v["discount"],
                        "shipping_total" => $v["shippingTotal"],
                        "processed_at_foreign" => date('c', $v['updatedAt']),
                        "updated_at_foreign" => date('c',$v['updatedAt']),
                        // "tracking_code" => $v["transactionId"],
                        // "processed_at_foreign" //
                        // "cancelled_at_foreign" 
                        // "cancelled_at_foreign" 
                        // "updated_at_foreign"
                        // "shipping_address"
                        // "billing_address"
                        // "promos"
                        // "outreach"
                        // "tracking_number"
                        // "tracking_carrier"
                        // "tracking_url"
                    ];
                    try {
                        // Insert Order
                        $addOrder = $mailchimp->service->ecommerce->addStoreOrder(
                            $storeID,
                            $dataInsert
                        );
                        if($addOrder){
                            dump('Insert đơn hàng ID: "'. $v["id"] .'" Thành công cho email: ' . $v["email"]);
                            Log::notice('Insert đơn hàng ID: "'. $v["id"] .'" Thành công cho email: ' . $v["email"]);
                        }
                    }catch (Exception $e) {
                        if($e->getResponse()->getStatusCode() == 400){
                            dump('Order đã '.$v["id"].' tồn tại vui lòng xóa hoăc bật xóa order để cập nhâp');
                            Log::notice('Order đã '.$v["id"].' tồn tại vui lòng xóa hoăc bật xóa order để cập nhâp');
                        }else{
                            Log::notice($e);
                        };
                    }
                }
            }
            break;
        } while (empty($data) === false);
        // sleep(10);
    }

    public function getOrderItems($order){
        $items = [];
        if(count($order['items'])){
            foreach($order['items'] as $key => $item){
                $items[] = $item;
            }
        }
        return $items;
    }

    public function getLines($order){
        $line = [];
        if(count($order['items'])){
            foreach($order['items'] as $item){
                $tempItem = [];
                $tempItem['id'] = $item['id'];
                $tempItem['product_id'] = $item['product']['id'];
                $tempItem['product_variant_id'] = $item['variant']['id'];
                $tempItem['quantity'] = $item['quantity'];
                $tempItem['price'] = $item['subTotal']; // $item['subTotal']
                $tempItem['discount'] = 0;
                $line[] = $tempItem;
            }
        }
        return $line;
    }

    public function totalPriceDraft($order){
        $priceDraft = 0;
        if(count($this->getLines($order))){
            foreach($this->getLines($order) as $value){
                $priceDraft+= $value['price'] ?? 0;
            }
        }
        return $priceDraft;
    }
}