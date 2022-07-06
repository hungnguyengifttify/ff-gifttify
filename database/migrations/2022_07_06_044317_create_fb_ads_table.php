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
        Schema::create('fb_ads', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('account_id');
            $table->json('adset');
            $table->unsignedBigInteger('adset_id');
            $table->string('bid_type');
            $table->json('campaign');
            $table->unsignedBigInteger('campaign_id');
            $table->string('configured_status');
            $table->string('conversion_domain');
            $table->json('conversion_specs');
            $table->dateTime('created_time');
            $table->json('creative');
            $table->unsignedBigInteger('creative_id');
            $table->string('demolink_hash');
            $table->integer('display_sequence');
            $table->string('effective_status');
            $table->tinyInteger('engagement_audience');
            $table->string('last_updated_by_app_id');
            $table->string('name');
            $table->string('preview_shareable_link');
            $table->json('source_ad');
            $table->unsignedBigInteger('source_ad_id');
            $table->string('status');
            $table->json('targeting');
            $table->json('tracking_and_conversion_with_defaults');
            $table->json('tracking_specs');
            $table->dateTime('updated_time');

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
        Schema::dropIfExists('fb_ads');
    }
};
