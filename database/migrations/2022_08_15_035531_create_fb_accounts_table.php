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
        Schema::create('fb_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('account_act_id')->nullable();
            $table->string('name')->nullable();
            $table->tinyInteger('account_status')->nullable();
            $table->string('store')->nullable();
            $table->float('age')->nullable();
            $table->double('amount_spent')->nullable();
            $table->double('balance')->nullable();
            $table->string('currency')->nullable();
            $table->tinyInteger('disable_reason')->nullable();
            $table->bigInteger('end_advertiser')->nullable();
            $table->string('end_advertiser_name')->nullable();
            $table->double('min_campaign_group_spend_cap')->nullable();
            $table->double('min_daily_budget')->nullable();
            $table->bigInteger('owner')->nullable();
            $table->bigInteger('spend_cap')->nullable();
            $table->integer('timezone_id')->nullable();
            $table->string('timezone_name')->nullable();
            $table->float('timezone_offset_hours_utc')->nullable();
            $table->string('business_city')->nullable();
            $table->string('business_country_code')->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_state')->nullable();
            $table->string('business_street')->nullable();
            $table->string('business_street2')->nullable();
            $table->string('business_zip')->nullable();
            $table->dateTime('created_time')->nullable();
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
        Schema::dropIfExists('fb_accounts');
    }
};
