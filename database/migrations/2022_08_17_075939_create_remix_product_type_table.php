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
        Schema::create('remix_product_type', function (Blueprint $table) {
            $table->string('id');
            $table->string('product_type')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->double('base_price')->nullable();
            $table->string('size_chart')->nullable();
            $table->string('category')->nullable();
            $table->string('gender')->nullable();
            $table->string('status')->nullable();
            $table->json('images')->nullable();
            $table->json('options')->nullable();
            $table->json('variants')->nullable();
            $table->timestamps();

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('remix_product_type');
    }
};
