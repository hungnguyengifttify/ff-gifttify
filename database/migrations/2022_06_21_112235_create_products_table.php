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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('store')->default('');
            $table->unsignedBigInteger('shopify_id')->default(0);
            $table->string('title')->default('');
            $table->longText('body_html')->nullable();
            $table->string('vendor')->default('');
            $table->string('product_type')->default('');
            $table->dateTime('shopify_created_at')->default('1900-01-01');
            $table->string('handle')->default('');
            $table->dateTime('shopify_updated_at')->default('1900-01-01');
            $table->dateTime('published_at')->default('1900-01-01');
            $table->string('template_suffix')->default('');
            $table->string('status')->default('');
            $table->string('published_scope')->default('');
            $table->string('tags', 512)->default('');
            $table->string('admin_graphql_api_id')->default('');
            $table->json('variants')->nullable();
            $table->json('options')->nullable();
            $table->json('images')->nullable();
            $table->json('image')->nullable();
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
        Schema::dropIfExists('products');
    }
};
