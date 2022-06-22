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
        Schema::create('order_line_items', function (Blueprint $table) {
            $table->id();
            $table->string('store')->default('');
            $table->unsignedBigInteger('shopify_id')->default(0);
            $table->unsignedBigInteger('order_id')->default(0);
            $table->unsignedBigInteger('product_id')->default(0);
            $table->unsignedBigInteger('variant_id')->default(0);
            $table->string('admin_graphql_api_id')->default('');
            $table->bigInteger('fulfillable_quantity')->default(0);
            $table->string('fulfillment_service')->default('');
            $table->string('fulfillment_status')->default('');
            $table->tinyInteger('gift_card')->default(0);
            $table->double('grams')->default(0);
            $table->string('name')->default('');
            $table->double('price')->default(0);
            $table->json('price_set')->nullable();
            $table->tinyInteger('product_exists')->default(0);
            $table->json('properties')->nullable();
            $table->bigInteger('quantity')->default(0);
            $table->tinyInteger('requires_shipping')->default(0);
            $table->string('sku')->default('');
            $table->tinyInteger('taxable')->default(0);
            $table->string('title')->default('');
            $table->bigInteger('total_discount')->default(0);
            $table->json('total_discount_set')->nullable();
            $table->string('variant_inventory_management')->default('');
            $table->string('variant_title')->default('');
            $table->string('vendor')->default('');
            $table->json('tax_lines')->nullable();
            $table->json('duties')->nullable();
            $table->json('discount_allocations')->nullable();
            $table->timestamps();

            $table->index('store');
            $table->index('shopify_id');
            $table->index('order_id');
            $table->index('product_id');
            $table->index('variant_id');
            $table->unique(array('store', 'shopify_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_line_items');
    }
};
