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
        Schema::create('fb_campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fb_campaign_id')->default(0);
            $table->string('name')->default('');
            $table->unsignedBigInteger('account_id')->default(0);
            $table->double('daily_budget')->default(0);
            $table->double('budget_remaining')->default(0);
            $table->string('status')->default('');
            $table->dateTime('start_time')->default('1900-01-01');
            $table->dateTime('updated_time')->default('1900-01-01');
            $table->string('bid_strategy')->default('');
            $table->string('configured_status')->default('');
            $table->string('effective_status')->default('');
            $table->string('objective')->default('');
            $table->string('buying_type')->default('');
            $table->string('special_ad_category')->default('');
            $table->timestamps();

            $table->unique('fb_campaign_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fb_campaigns');
    }
};
