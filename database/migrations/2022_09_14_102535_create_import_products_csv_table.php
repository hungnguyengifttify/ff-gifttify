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
        Schema::create('import_products_csv', function (Blueprint $table) {
            $table->id();
            $table->string('shopifyId');
            $table->string('returnedId')->nullable(true);
            $table->string('slug');
            $table->string('title');
            $table->string('productType');
            $table->string('status');
            $table->string('tags');
            $table->json('tagsArr');
            $table->json('images');
            $table->json('options');
            $table->json('variants');
            $table->json('seo');
            $table->json('s3Images')->nullable(true);
            $table->tinyInteger('syncedStatus')->default(0);
            $table->dateTime('syncedStatusTime')->nullable(true);
            $table->tinyInteger('syncedImage')->default(0);
            $table->dateTime('syncedImageTime')->nullable(true);

            $table->timestamps();
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_products_csv');
    }
};
