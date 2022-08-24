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
            $table->unsignedInteger('wp_id')->default(0);
            
            $table->string('name')->default('')->nullable();
            $table->string('slug')->default('')->nullable();
            $table->string('permalink')->default('')->nullable();
            $table->dateTime('date_created')->default('1900-01-01');
            $table->dateTime('date_created_gmt')->default('1900-01-01');
            $table->dateTime('date_modified')->default('1900-01-01');
            $table->dateTime('date_modified_gmt')->default('1900-01-01');
            $table->string('type')->default('')->nullable();
            $table->string('status')->default('')->nullable();
            $table->string('featured')->default(false);
            $table->string('catalog_visibility')->default('')->nullable();
            $table->longText('description')->nullable();
            $table->string('short_description')->default('')->nullable();
            $table->string('sku')->default('')->nullable();
            $table->double('price')->default(0)->nullable();
            $table->double('regular_price')->default(0)->nullable();
            $table->double('sale_price')->default(0)->nullable();
            $table->dateTime('date_on_sale_from')->default('1900-01-01')->nullable();
            $table->dateTime('date_on_sale_from_gmt')->default('1900-01-01')->nullable();
            $table->dateTime('date_on_sale_to')->default('1900-01-01')->nullable();
            $table->dateTime('date_on_sale_to_gmt')->default('1900-01-01')->nullable();
            $table->string('price_html')->default('')->nullable();
            $table->string('on_sale')->default('')->nullable();
            $table->string('purchasable')->default('')->nullable();
            $table->string('total_sales')->default('')->nullable();
            $table->string('virtual')->default('')->nullable();
            $table->string("downloadable")->default('')->nullable();
            $table->string("downloads")->default('')->nullable();
            $table->string("download_limit")->default('')->nullable();
            $table->string("download_expiry")->default('')->nullable();
            $table->string("external_url")->default('')->nullable();
            $table->string("button_text")->default('')->nullable();
            $table->string("tax_status")->default('')->nullable();
            $table->string("tax_class")->default('')->nullable();
            $table->string("manage_stock")->default('')->nullable();
            $table->string("stock_quantity")->default('')->nullable();
            $table->string("stock_status")->default('')->nullable();
            $table->string("backorders")->default('')->nullable();
            $table->string("backorders_allowed")->default('')->nullable();
            $table->string("backordered")->default('')->nullable();
            $table->string("sold_individually")->default('')->nullable();
            $table->string("weight")->default('')->nullable();
            $table->json("dimensions")->nullable();
            $table->string("shipping_required")->default('')->nullable();
            $table->string("shipping_taxable")->default('')->nullable();
            $table->string("shipping_class")->default('')->nullable();
            $table->string("shipping_class_id")->default('')->nullable();
            $table->string("reviews_allowed")->default('')->nullable();
            $table->float("average_rating")->default(0)->nullable();
            $table->integer("rating_count")->default(0)->nullable();
            $table->json("related_ids")->nullable();
            $table->json("upsell_ids")->nullable();
            $table->json("cross_sell_ids")->nullable();
            $table->integer("parent_id")->default(0); //Parent
            $table->string("purchase_note")->default('')->nullable();
            $table->json('categories')->nullable();
            $table->json('tags')->nullable();
            $table->json('images')->nullable();
            $table->json('attributes')->nullable();
            $table->json('default_attributes')->nullable();
            $table->json('variations')->nullable();
            $table->json('grouped_products')->nullable();
            $table->integer('menu_order')->default(0);
            $table->json('meta_data')->nullable();
            $table->json('collection')->nullable();

            $table->index('store')->nullable();
            $table->index('date_created');
            $table->unique(array('store', 'wp_id'));
            $table->timestamps();
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
