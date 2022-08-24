<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wp_products', function (Blueprint $table) {
            $table->id();
            $table->string('store')->default('');
            $table->string('name')->default('');
            $table->string('permalink')->default('');
            $table->string('date_created')->default('');
            $table->string('date_created_gmt')->default('');
            $table->string('date_modified')->default('');
            $table->string('date_modified_gmt')->default('');
            $table->string('type')->default('');
            $table->string('status')->default('');
            $table->boolean('featured')->default('');
            $table->string('catalog_visibility')->default('');
            $table->string('description')->default('');
            $table->string('short_description')->default('');
            $table->string('sku')->default('');
            $table->string('price')->default('');
            $table->string('regular_price')->default('');
            $table->string('sale_price')->default('');
            $table->string('short_description')->default('');
            $table->string('sku')->default('');
            $table->string('price')->default('');
            $table->string('regular_price')->default('');
            $table->string('sale_price')->default('');
            $table->string('date_on_sale_from')->default('');
            $table->string('date_on_sale_from_gmt')->default('');
            $table->string('date_on_sale_to')->default('');
            $table->string('date_on_sale_to_gmt')->default('');
            $table->string('price_html')->default('');
            $table->string('on_sale')->default('');
            $table->string('purchasable')->default('');
            $table->string('total_sales')->default('');
            $table->string('virtual')->default('');
            $table->string("download_limit")->default('');
            $table->string("download_expiry")->default('');
            $table->string("external_url")->default('');
            $table->string("button_text")->default('');
            $table->string("tax_status")->default('');
            $table->string("tax_class")->default('');
            $table->string("manage_stock")->default('');
            $table->string("stock_quantity")->default('');
            $table->string("stock_status")->default('');
            $table->string("backorders")->default('');
            $table->string("backorders_allowed")->default('');
            $table->string("backordered")->default('');
            $table->string("sold_individually")->default('');
            $table->string("weight")->default('');
            $table->string("shipping_required")->default('');
            $table->string("shipping_taxable")->default('');
            $table->string("shipping_class")->default('');
            $table->string("shipping_class_id")->default('');
            $table->string("reviews_allowed")->default('');
            $table->string("average_rating")->default('');
            $table->string("rating_count")->default('');
            $table->string("upsell_ids")->default('');
            $table->string("cross_sell_ids")->default('');
            $table->string("parent_id")->default('');
            $table->string("purchase_note")->default('');

            $table->json('categories')->nullable();
            $table->json('tags')->nullable();
            $table->json('images')->nullable();
            $table->json('attributes')->nullable();
            $table->json('dimensions')->nullable();

            $table->index('store');
            $table->index('shopify_created_at');
            $table->unique(array('store', 'shopify_id'));


            $table->timestamps();

           $a = [
               "id"=> 801,
              "name"=> "Woo Single #1",
              "slug"=> "woo-single-1-4",
              "permalink"=> "https://example.com/product/woo-single-1-4/",
              "date_created"=> "2017-03-23T17:35:43",
              "date_created_gmt"=> "2017-03-23T20:35:43",
              "date_modified"=> "2017-03-23T17:35:43",
              "date_modified_gmt"=> "2017-03-23T20:35:43",
              "type"=> "simple",
              "status"=> "publish",
              "featured"=> false,
              "catalog_visibility"=> "visible",
              "description"=> "",
              "short_description"=> "",
              "sku"=> "",
              "price"=> "21.99",
              "regular_price"=> "21.99",
              "sale_price"=> "",
              "date_on_sale_from"=> null,
              "date_on_sale_from_gmt"=> null,
              "date_on_sale_to"=> null,
              "date_on_sale_to_gmt"=> null,
              "price_html"=> "<span class=\"woocommerce-Price-amount amount\"><span class=\"woocommerce-Price-currencySymbol\">&#36;</span>21.99</span>",
              "on_sale"=> false,
              "purchasable"=> true,
              "total_sales"=> 0,
              "virtual"=> true,
              "download_limit"=> -1,
              "download_expiry"=> -1,
              "external_url"=> "",
              "button_text"=> "",
              "tax_status"=> "taxable",
              "tax_class"=> "",
              "manage_stock"=> false,
              "stock_quantity"=> null,
              "stock_status"=> "instock",
              "backorders"=> "no",
              "backorders_allowed"=> false,
              "backordered"=> false,
              "sold_individually"=> false,
              "weight"=> "",

              "shipping_required"=> false,
              "shipping_taxable"=> true,
              "shipping_class"=> "",
              "shipping_class_id"=> 0,
              "reviews_allowed"=> true,
              "average_rating"=> "0.00",
              "rating_count"=> 0,
              "upsell_ids"=> [],
              "cross_sell_ids"=> [],
              "parent_id"=> 0,
              "purchase_note"=> "",



//            $table->id();
//            $table->string('store')->default('');
//            $table->unsignedBigInteger('shopify_id')->default(0);
//            $table->string('title')->default('');
//            $table->longText('body_html')->nullable();
//            $table->string('vendor')->default('');
//            $table->string('product_type')->default('');
//            $table->dateTime('shopify_created_at')->default('1900-01-01');
//            $table->string('handle')->default('');
//            $table->dateTime('shopify_updated_at')->default('1900-01-01');
//            $table->dateTime('published_at')->default('1900-01-01');
//            $table->string('template_suffix')->default('');
//            $table->string('status')->default('');
//            $table->string('published_scope')->default('');
//            $table->string('tags', 512)->default('');
//            $table->string('admin_graphql_api_id')->default('');
//            $table->json('variants')->nullable();
//            $table->json('options')->nullable();
//            $table->json('images')->nullable();
//            $table->json('image')->nullable();
//            $table->timestamps();
//

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wp_products');
    }
};
