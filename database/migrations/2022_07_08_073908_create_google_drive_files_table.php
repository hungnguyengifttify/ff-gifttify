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
        Schema::create('google_drive_files', function (Blueprint $table) {
            $table->string('id');
            $table->dateTime('createdTime');
            $table->dateTime('modifiedTime');
            $table->string('description', 512);
            $table->string('fullFileExtension');
            $table->string('mimeType');
            $table->string('name');
            $table->string('parentId');
            $table->string('thumbnailLink');
            $table->string('webViewLink');
            $table->string('webContentLink');
            $table->tinyInteger('viewersCanCopyContent');
            $table->tinyInteger('writersCanShare');
            $table->integer('size');
            $table->json('parents');
            $table->json('owners');
            $table->json('spaces');
            $table->json('permissionIds');
            $table->timestamps();

            $table->primary('id');
            $table->index('parentId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_drive_files');
    }
};
