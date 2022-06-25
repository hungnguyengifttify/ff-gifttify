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
        Schema::create('gifttify_code', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->string('code');
            $table->timestamps();
            $table->unique(array('type', 'code'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_code');
    }
};
