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
        Schema::create('ga_campaign_reports', function (Blueprint $table) {
            $table->id();
            $table->string('campains_name');
            $table->integer('view_id');
            $table->date('date_record');
            $table->integer('users');
            $table->integer('new_users');
            $table->integer('session');
            $table->float('bounce_rate');
            $table->float('pageviews_per_session');
            $table->float('avg_session_duration');
            $table->float('goal_conversion_rate_all');
            $table->integer('goal_completions_all');
            $table->float('goal_value_all');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ga_campaign_reports');
    }
};
