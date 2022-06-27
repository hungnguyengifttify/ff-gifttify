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
        Schema::create('campaign_product_type', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_name');
            $table->string('product_type_code');
            $table->timestamps();

            $table->unique(array('campaign_name', 'product_type_code'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_product_type');
    }
};
