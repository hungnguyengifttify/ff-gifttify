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
        Schema::dropIfExists('wp_order_line_items');
        Schema::create('wp_order_line_items', function (Blueprint $table) {
            $table->id();
            
            $table->string('store')->default('');
            $table->unsignedInteger('wp_id')->default(0);
            $table->unsignedBigInteger('order_id')->default(0);

            $table->string('name')->default('');
            $table->unsignedInteger('product_id')->default(0);
            $table->unsignedInteger('variation_id')->default(0);
            $table->integer('quantity')->default(0);
            $table->string('tax_class')->default('');
            $table->double('subtotal')->default(0);
            $table->double('subtotal_tax')->default(0);
            $table->double('total')->default(0);
            $table->double('total_tax')->default(0);
            $table->json('taxes')->nullable();
            $table->json('meta_data')->nullable();
            $table->string('sku')->default('');
            $table->double('price')->default(0);
            $table->timestamps();

            $table->index('store');
            $table->index('wp_id');
            $table->index('order_id');
            $table->index('product_id');
            $table->index('variation_id');
            $table->unique(array('store', 'wp_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wp_order_line_items');
    }
};
