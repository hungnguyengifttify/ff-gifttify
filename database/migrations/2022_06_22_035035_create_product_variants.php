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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('store')->default('');
            $table->unsignedBigInteger('shopify_id')->default(0);
            $table->unsignedBigInteger('product_id')->default(0);
            $table->dateTime('shopify_created_at')->default('1900-01-01');
            $table->dateTime('shopify_updated_at')->default('1900-01-01');
            $table->string('barcode')->default('');
            $table->double('compare_at_price')->default(0);
            $table->string('fulfillment_service')->default('');
            $table->double('grams')->default(0);
            $table->double('weight')->default(0);
            $table->string('weight_unit')->default('');
            $table->unsignedBigInteger('inventory_item_id')->default(0);
            $table->string('inventory_management')->default('');
            $table->string('inventory_policy')->default('');
            $table->double('inventory_quantity')->default(0);
            $table->string('option1')->default('');
            $table->string('option2')->default('');
            $table->string('option3')->default('');
            $table->integer('position')->default(0);
            $table->double('price')->default(0);
            $table->tinyInteger('requires_shipping')->default(0);
            $table->string('sku')->default('');
            $table->tinyInteger('taxable')->default(0);
            $table->string('title')->default('');
            $table->timestamps();

            $table->index('store');
            $table->index('product_id');
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
        Schema::dropIfExists('product_variants');
    }
};
