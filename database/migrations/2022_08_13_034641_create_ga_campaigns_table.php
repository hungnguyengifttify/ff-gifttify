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
        Schema::create('ga_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('campainsName');
            $table->dateTime('viewId');
            $table->int('day');
            $table->int('month');
            $table->int('year');
            $table->string('user');
            $table->string('newUser');
            $table->string('session');
            $table->string('avgSessionDuration');
            $table->string('bounceRate');
            $table->string('goalCompletionsAll');
            $table->string('goalValueAll');
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
        Schema::dropIfExists('ga_campaigns');
    }
};
