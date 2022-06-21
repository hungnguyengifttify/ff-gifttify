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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('store');
            $table->unsignedBigInteger('shopify_id');
            $table->string('admin_graphql_api_id');
            $table->unsignedBigInteger('app_id');
            $table->string('browser_ip');
            $table->tinyInteger('buyer_accepts_marketing');
            $table->string('cancel_reason');
            $table->dateTime('cancelled_at');
            $table->string('cart_token');
            $table->unsignedBigInteger('checkout_id');
            $table->string('checkout_token');
            $table->json('client_details');
            $table->dateTime('closed_at');
            $table->tinyInteger('confirmed');
            $table->string('contact_email');
            $table->dateTime('shopify_created_at');
            $table->string('currency');
            $table->double('current_subtotal_price');
            $table->json('current_subtotal_price_set');
            $table->double('current_total_discounts');
            $table->json('current_total_discounts_set');
            $table->json('current_total_duties_set');
            $table->double('current_total_price');
            $table->json('current_total_price_set');
            $table->double('current_total_tax');
            $table->json('current_total_tax_set');
            $table->string('customer_locale');
            $table->string('device_id');
            $table->json('discount_codes');
            $table->string('email');
            $table->string('estimated_taxes');
            $table->string('financial_status');
            $table->string('fulfillment_status');
            $table->string('gateway');
            $table->text('landing_site');
            $table->string('landing_site_ref');
            $table->string('location_id');
            $table->string('name');
            $table->text('note');
            $table->json('note_attributes');
            $table->integer('number');
            $table->integer('order_number');
            $table->string('order_status_url');
            $table->json('original_total_duties_set');
            $table->json('payment_gateway_names');
            $table->string('phone');
            $table->string('presentment_currency');
            $table->dateTime('processed_at');
            $table->string('processing_method');
            $table->string('reference');
            $table->string('source_identifier');
            $table->string('source_name');
            $table->string('source_url');
            $table->double('subtotal_price');
            $table->json('subtotal_price_set');
            $table->string('tags');
            $table->json('tax_lines');
            $table->string('taxes_included');
            $table->string('test');
            $table->string('token');
            $table->double('total_discounts');
            $table->json('total_discounts_set');
            $table->double('total_line_items_price');
            $table->json('total_line_items_price_set');
            $table->double('total_outstanding');
            $table->double('total_price');
            $table->json('total_price_set');
            $table->double('total_price_usd');
            $table->json('total_shipping_price_set');
            $table->double('total_tax');
            $table->json('total_tax_set');
            $table->double('total_tip_received');
            $table->double('total_weight');
            $table->dateTime('shopify_updated_at');
            $table->string('user_id');
            $table->json('customer');
            $table->json('discount_applications');
            $table->json('fulfillments');
            $table->json('line_items');
            $table->string('payment_terms');
            $table->json('refunds');
            $table->json('shipping_address');
            $table->json('shipping_lines');

            $table->timestamps();

            $table->index('store');
            $table->index('shopify_created_at');
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
        Schema::dropIfExists('orders');
    }
};
