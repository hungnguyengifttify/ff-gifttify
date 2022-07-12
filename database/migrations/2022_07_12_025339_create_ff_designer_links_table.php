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
        Schema::create('ff_designer_links', function (Blueprint $table) {
            $table->id();
            $table->dateTime('request_date');
            $table->string('image_link');
            $table->string('ref');
            $table->string('product_type');
            $table->string('store');
            $table->string('product_note', 512);
            $table->string('link');
            $table->string('designer');
            $table->string('status');
            $table->string('staff_note', 512);
            $table->string('reason_note', 512);
            $table->string('sheet');
            $table->timestamps();

            $table->unique('ref');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ff_designer_links');
    }
};
