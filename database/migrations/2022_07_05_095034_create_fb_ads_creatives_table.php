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
        Schema::create('fb_ads_creatives', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger("account_id")->default(0);
            $table->unsignedBigInteger("actor_id")->default(0);
            $table->text('body');
            $table->string('call_to_action_type')->default('');
            $table->string('effective_object_story_id')->default('');
            $table->json('image_crops');
            $table->string('image_hash')->default('');
            $table->string('image_url', 1024)->default('');
            $table->string('name')->default('');
            $table->json('object_story_spec');
            $table->string('object_type')->default('');
            $table->string('status')->default('');
            $table->string('thumbnail_url', 1024)->default('');
            $table->string('title')->default('');
            $table->string('url_tags')->default('');

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
        Schema::dropIfExists('fb_ads_creatives');
    }
};
