<?php

namespace App\Services;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Config;
use MailchimpMarketing\ApiClient;
use MailchimpMarketing\ObjectSerializer;

class MailChimpService
{
    public $service;

    public $dateRange;
    public $metrics = [];


    public $viewID = '230760666';

    function __construct($config=[])
    {
        $this->service = new ApiClient();
        $apiKey = Config::get('mailchimp.apiKey');
        $server = Config::get('mailchimp.server');
        if(!empty($config)) {
            $apiKey = $config['apiKey'];
            $server = $config['server'];
        }
        $this->service ->setConfig([
            'apiKey' => $apiKey,
            'server' => $server
        ]);
    }

    //Lây thông tin store
    public function getStore($store_id, $fields = null, $exclude_fields = null)
    {
        $response = $this->service->ecommerce->getStoreWithHttpInfo($store_id, $fields, $exclude_fields);
        return $response;
    }

    //=== Order Manager API ==========================
    // Lấy thông tin order cả account
    function getListAccountOrders(){
        $response = $this->service->ecommerce->ecommerce->orders();
        return $response;
    }

    //Lấy tất cả order thuộc 1 store
    function getStoreOrders($store_id, $fields = null, $exclude_fields = null, $count = '10', $offset = '0', $customer_id = null, $has_outreach = null, $campaign_id = null, $outreach_id = null){
         $response = $this->service->ecommerce->getStoreOrders($store_id);
        return $response;
    }

    // Get order info
    public function getOrder($store_id, $order_id, $fields = null, $exclude_fields = null)
    {
        $response = $this->service->ecommerce->getOrderWithHttpInfo($store_id, $order_id, $fields, $exclude_fields);
        return $response;
    }

    // Delete an order.
    public function deleteOrder($store_id, $order_id)
    {
        $response = $this->service->ecommerce->deleteOrderWithHttpInfo($store_id, $order_id);
    }

    // Add a new order to a store.
    // EG:
    // $response = $client->ecommerce->addStoreOrder("store_id", [
    //     "id" => "id",
    //     "customer" => ["id" => "id"],
    //     "currency_code" => "currency_code",
    //     "order_total" => 75895,
    //     "lines" => [
    //         [
    //             "id" => "id",
    //             "product_id" => "product_id",
    //             "product_variant_id" => "product_variant_id",
    //             "quantity" => 990,
    //             "price" => 43106,
    //         ],
    //     ],
    // ]);

    public function addStoreOrder($store_id, $body)
    {
        $response = $this->service->ecommerce->addStoreOrderWithHttpInfo($store_id, $body);
        return $response;
    }

    // Update a specific order.
    public function updateOrder($store_id, $order_id, $body)
    {
        $response = $this->service->ecommerce->updateOrderWithHttpInfo($store_id, $order_id, $body);
        return $response;
    }

    //=== Product Manager API ==========================

    // Get information about a store's products.
    function getAllStoreProducts($store_id, $fields = null, $exclude_fields = null, $count = '10', $offset = '0'){
        $response = $this->service->ecommerce->getAllStoreProducts($store_id, $fields = null, $exclude_fields = null, $count = '10', $offset = '0');
        return $response;
    }

    // Get information about a specific product.
    public function getStoreProduct($store_id, $product_id, $fields = null, $exclude_fields = null)
    {
        $response = $this->service->ecommerce->getStoreProductWithHttpInfo($store_id, $product_id, $fields, $exclude_fields);
        return $response;
    }



    // Get information about a store's products.
    // EG:
    // $response = $client->ecommerce->addStoreProduct("store_id", [
    //     "id" => "id",
    //     "title" => "Cat Hat",
    //     "variants" => [["id" => "id", "title" => "Cat Hat"]],
    // ]);

    public function addStoreProduct($store_id, $body)
    {
        $response = $this->service->ecommerce->addStoreProductWithHttpInfo($store_id,$body);
        return $response;
    }

    //Update a specific product.
    public function updateStoreProduct($store_id, $product_id, $body)
    {
        $response = $this->service->ecommerce->updateStoreProductWithHttpInfo($store_id, $product_id, $body);
        return $response;
    }

    // Delete a product.
    public function deleteStoreProduct($store_id, $product_id)
    {
        $response = $this->service->ecommerce->deleteStoreProductWithHttpInfo($store_id, $product_id);
        return $response;
    }

}
