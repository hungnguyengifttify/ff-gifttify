<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use App\Models\RedisGtf;
use Illuminate\Console\Command;
use App\Services\MailChimpService;
use Carbon\Carbon;
use Exception;

class UploadOrderToMailChimp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'mailchimp:push_order
        {--mode=1} {--server=} {--apiKey=} {--siteDomain=} {--storeID=} {--redisDB=}';


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
    public function handle(){
        $mode = (int)$this->option('mode');
        if ($mode == 1) {
            $this->handleModeDefault();
        } else {
            $this->handleModeAll();
        }
    }

    public function handleModeAll(){
        $server = trim($this->option('server'));
        $apiKey = trim($this->option('apiKey'));
        $siteDomain = trim($this->option('siteDomain'));
        $storeID = trim($this->option('storeID'));
        $redisDB = (int)$this->option('redisDB');
        if(!$server || !$apiKey || !$siteDomain || !$storeID || $redisDB <= 0){
            dump('Error: Invalid parameters');
            return;
        }
        $this->handlePushOrderToMailchimp($server, $apiKey, $siteDomain, $storeID, $redisDB, false);
    }

    public function handleModeDefault(){
        $configMailchimp = config('mailchimp');
        if(!empty($configMailchimp['servers'])){
            $listServer = explode(',', $configMailchimp['servers']);
            $listApiKey = explode(',', $configMailchimp['apiKeys']);
            $listStoreSite = explode(',', $configMailchimp['storeSites']);
            $listStoreId = explode(',', $configMailchimp['storeIds']);
            $listRedisDB = explode(',', $configMailchimp['redisDbs']);
            for($i=0; $i < count($listServer); $i++){
                $server = $listServer[$i];
                $apiKey = $listApiKey[$i];
                $siteDomain = $listStoreSite[$i];
                $storeID = $listStoreId[$i];
                $redisDB = $listRedisDB[$i];
                $this->handlePushOrderToMailchimp($server, $apiKey, $siteDomain, $storeID, $redisDB);
            }
        }else{
            $server = $configMailchimp['server'];
            $apiKey = $configMailchimp['apiKey'];
            $siteDomain = 'https://thecreattify.com';
            $storeID = 'store_k6gosw5gwhooezt3i61m';
            $redisDB = 1;
            $this->handlePushOrderToMailchimp($server, $apiKey, $siteDomain, $storeID, $redisDB);
        }
    }

    public function handlePushOrderToMailchimp($server, $apiKey, $siteDomain, $storeID, $redisDB, $modeDefault=true){
        $pageOrder = 0;
        $pageLimit = 30;
        $mailchimp = new MailChimpService(['server' => $server, 'apiKey' => $apiKey]);
        // Code check
        // $response = $mailchimp->service->ecommerce->stores();
        // $a = $mailchimp->service->ecommerce->deleteStoreCart($storeID, 'TC_JWYIcY1Zq');
        // $b = $mailchimp->service->ecommerce->getStoreCart($storeID, 'TC_rlizJyftb');
        // $b = $mailchimp->service->ecommerce->getOrder($storeID, 'TC_rlizJyftb');
        // $b = $mailchimp->service->ecommerce->getStoreCarts($storeID);
        // dd($b);
        do {
            $pageOrder++;
            $data = RedisGtf::getRedisOrdersList($redisDB, [], '', $pageOrder, $pageLimit, true);
            foreach ($data['results'] as $v) {

                // Code check
                // if($v['email'] != 'hatv1592@gmail.com'){
                //     continue;
                // }

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
                                        'url' => $siteDomain.'/products/' . $productInfo['product']['slug'] . '-' .  $productInfo['product']['id'],
                                        'sku' => $productInfo['variant']['sku'],
                                        'price' => $productInfo['variant']['price'],
                                        'inventory_quantity' => $productInfo['variant']['quantity'],
                                        'image_url' => $productInfo['product']['images'][0]['src'] ?? '',
                                        ]
                                    ], // REQUIRE
                                    "handle" =>  "API_PUSH", //The handle of a product.
                                    "url" => $siteDomain.'/products/' . $productInfo['product']['slug'] . '-' .  $productInfo['product']['id'],
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

                // Import or update Customer
                try{
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
                }catch(Exception $e){
                    dump('Insert/update : "'. $v["id"] .'" không thành công email: ' . $v["email"]);
                }

                // Import/Update Cart
                if($v["status"] == 'draft'){
                    try {
                        $isExitsCart = $mailchimp->service->ecommerce->getStoreCart($storeID, $v["id"]);
                    }catch (Exception $e) {
                        $isExitsCart = false;
                    }

                    // Show all cart
                    // $mailchimp->service->ecommerce->getStoreCarts($storeID);

                    $dataInsert = [
                        "id" => $v["id"],
                        "customer" => [
                            "id" => $v["email"], //A unique identifier for the customer. Limited to 50 characters.
                        ], //Information about a specific customer. For existing customers include only the id parameter in the customer object body.
                        "currency_code" => $v["currency"]["code"] ?? 'USD',
                        "order_total" =>  $this->totalPriceDraft($v), // The order total for the cart.
                        "lines" => $this->getLines($v),
                        "checkout_url" => $siteDomain . '/checkout/' . $v["id"] // A string that uniquely identifies the campaign for an order.
                    ];

                    try {
                        if(!$isExitsCart){
                            $addCart = $mailchimp->service->ecommerce->addStoreCart(
                                $storeID,
                                $dataInsert
                            );
                            dump('Insert cart ID: "'. $v["id"] .'" Thành công cho email: ' . $v["email"]);
                        }else{
                            $updateCart = $mailchimp->service->ecommerce->updateStoreCart(
                                $storeID,
                                $v["id"],
                                $dataInsert
                            );
                            dump('Update cart ID: "'. $v["id"] .'" Thành công cho email: ' . $v["email"]);
                        }
                    }catch (Exception $e) {
                        Log::info('Import/Upate cart ID: '.$v["id"]. ' - '. $v["email"].' thất bại.');
                        dump('Import/Upate cart ID: '.$v["id"]. ' - '. $v["email"].' thất bại.');
                    }
                }

                // checkExits Order
                try {
                    $isExitsOrder = $mailchimp->service->ecommerce->getOrder($storeID, $v["id"]);
                }catch (Exception $e) {
                    $isExitsOrder = false;
                }

                $dataInsert = [
                    "id" => $v["id"],
                    "customer" => [
                        "id" => $v["email"], //A unique identifier for the customer. Limited to 50 characters.
                    ], //Information about a specific customer. For existing customers include only the id parameter in the customer object body.
                    "currency_code" => $v["currency"]["code"] ?? 'USD',
                    "order_total" =>  $v["total"] ?? $this->totalPriceDraft($v), // The total for the order.
                    "lines" => $this->getLines($v),

                    // "campaign_id" // A string that uniquely identifies the campaign for an order.
                    // "landing_site" // The URL for the page where the buyer landed when entering the shop.

                    "financial_status" => (isset($v['paidAt']) && $v['paidAt'] != null) ? 'paid' : 'pending', //paid, pending, refunded, cancelled
                    "fulfillment_status" => $v["status"],

                    "discount_total" => $v["discount"] ?? 0,
                    "shipping_total" => $v["shippingTotal"] ?? 0,
                    "processed_at_foreign" => Carbon::createFromTimestamp($v['createdAt']/1000)->toDateTimeString(),
                    "updated_at_foreign" => Carbon::createFromTimestamp($v['updatedAt']/1000)->toDateTimeString(),
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
                // Insert/Update Order
                try {
                    // Insert Order
                    if(!$isExitsOrder){
                        $mailchimp->service->ecommerce->addStoreOrder(
                            $storeID,
                            $dataInsert
                        );
                        dump('Insert đơn hàng ID: "'. $v["id"] .'" Thành công cho email: ' . $v["email"]);
                    }else{
                        $mailchimp->service->ecommerce->updateOrder(
                            $storeID,
                            $v["id"],
                            $dataInsert
                        );

                        if(isset($isExitsOrder->fulfillment_status) && $isExitsOrder->fulfillment_status == 'draft' && $v["status"] == 'completed') {
                            try {
                            $mailchimp->service->ecommerce->deleteStoreCart($storeID, $v["id"]);
                            dump('Xóa cart ID (Đơn draft -> completed): "'. $v["id"] .'" Thành công cho email: ' . $v["email"]);
                            }catch (Exception $e) {

                            }
                        };

                        dump('Update đơn hàng ID: "'. $v["id"] .'" Thành công cho email: ' . $v["email"]);
                    }
                }catch (Exception $e) {
                    Log::info('Import/Upate cart ID: '.$v["id"]. ' - '. $v["email"].' thất bại.');
                    dump('Insert/update order '.$v["id"].' email: ' . $v["email"].' thất bại');
                }
            }
            if($modeDefault){
                break;
            }
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
