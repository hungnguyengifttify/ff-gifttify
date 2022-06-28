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
        Schema::create('fb_ad_sets', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->default(0);
            $table->bigInteger('campaign_id')->default(0);
            $table->bigInteger('account_id')->default(0);
            $table->string('name')->default('');
            $table->string('status')->default('');
            $table->string('configured_status')->default('');
            $table->string('effective_status')->default('');
            $table->double('daily_budget')->default(0);
            $table->timestamps();

            $table->primary('id');
            $table->index('campaign_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fb_ad_sets');
    }
};
