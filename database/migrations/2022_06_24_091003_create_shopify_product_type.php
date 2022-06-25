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
        Schema::create('shopify_product_type', function (Blueprint $table) {
            $table->id();
            $table->string('product_type_name');
            $table->string('product_type_code');
            $table->timestamps();

            $table->unique(array('product_type_name', 'product_type_code'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shopify_product_type');
    }
};
