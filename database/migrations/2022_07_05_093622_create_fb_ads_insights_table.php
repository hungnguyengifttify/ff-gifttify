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
        Schema::create('fb_ads_insights', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->string('account_name');
            $table->string('account_currency');
            $table->unsignedBigInteger('ad_id');
            $table->string('ad_name');
            $table->unsignedBigInteger('adset_id');
            $table->string('adset_name');
            $table->unsignedBigInteger('campaign_id');
            $table->string('campaign_name');
            $table->string('country');
            $table->float('cpc');
            $table->float('cpm');
            $table->float('cpp');
            $table->float('ctr');
            $table->date('date_record');
            $table->integer('impressions');
            $table->string('objective');
            $table->integer('reach');
            $table->float('spend');
            $table->integer('inline_link_clicks');
            $table->integer('unique_clicks');
            $table->float('unique_link_clicks_ctr');
            $table->integer('clicks');
            $table->timestamps();

            $table->index('ad_id');
            $table->index('campaign_id');
            $table->index('date_record');
            $table->unique(['ad_id', 'campaign_id', 'country', 'date_record']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fb_ads_insights');
    }
};
