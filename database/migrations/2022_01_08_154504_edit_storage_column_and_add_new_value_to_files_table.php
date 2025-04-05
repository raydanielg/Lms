<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditStorageColumnAndAddNewValueToFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `files` MODIFY COLUMN `storage` enum('upload', 'youtube', 'vimeo', 'external_link', 'google_drive', 'iframe', 's3','upload_archive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `downloadable`");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `files` MODIFY COLUMN `file` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `storage`");

            $table->enum('interactive_type', ['adobe_captivate', 'i_spring', 'custom'])->nullable()->after('file_type');
            $table->string('interactive_file_name')->nullable()->after('interactive_type');
            $table->string('interactive_file_path')->nullable()->after('interactive_file_name');
        });
    }
}
