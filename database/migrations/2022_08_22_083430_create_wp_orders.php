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
        Schema::dropIfExists('wp_orders');
        Schema::create('wp_orders', function (Blueprint $table) {
            $table->id();
            $table->string('store')->default('');

            $table->unsignedInteger('wp_id')->default(0);
            $table->unsignedInteger('parent_id')->default(0);
            $table->string('number')->nullable();
            $table->string('order_key')->nullable();
            $table->string('created_via')->nullable();
            $table->string('version')->nullable();
            $table->string('status')->nullable();
            $table->string('currency')->nullable();
            $table->dateTime('date_created')->default('1900-01-01');
            $table->dateTime('date_created_gmt')->default('1900-01-01');
            $table->dateTime('date_modified')->default('1900-01-01');
            $table->dateTime('date_modified_gmt')->default('1900-01-01');
            $table->float('discount_total')->default(0)->nullable();
            $table->float('discount_tax')->default(0)->nullable();
            $table->float('shipping_total')->default(0)->nullable();
            $table->float('shipping_tax')->default(0)->nullable();
            $table->float('cart_tax')->default(0)->nullable();
            $table->float('total')->default(0)->nullable();
            $table->float('total_tax')->default(0)->nullable();
            $table->string('prices_include_tax')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->string('customer_ip_address')->nullable();
            $table->string('customer_user_agent')->nullable();
            $table->string('customer_note')->nullable();
            $table->json('billing')->nullable();
            $table->json('shipping')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_method_title')->nullable();
            $table->string('transaction_id')->nullable();
            $table->dateTime('date_paid')->default('1900-01-01')->nullable();
            $table->dateTime('date_paid_gmt')->default('1900-01-01')->nullable();
            $table->dateTime('date_completed')->default('1900-01-01')->nullable();
            $table->dateTime('date_completed_gmt')->default('1900-01-01')->nullable();
            $table->string('cart_hash')->nullable();
            $table->json('meta_data')->nullable();
            $table->json('line_items')->nullable();
            $table->json('shipping_lines')->nullable();
            $table->json('fee_lines')->nullable();
            $table->json('coupon_lines')->nullable();
            $table->json('refunds')->nullable();
            $table->json('_links')->nullable();

            $table->index('store');
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
        Schema::dropIfExists('wp_orders');
    }
};
