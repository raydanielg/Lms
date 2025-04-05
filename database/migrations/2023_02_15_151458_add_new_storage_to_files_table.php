<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewStorageToFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `files` MODIFY COLUMN `storage` enum('upload', 'youtube', 'vimeo', 'external_link', 'google_drive', 'dropbox', 'iframe', 's3', 'upload_archive', 'secure_host') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `downloadable`");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            //
        });
    }
}
