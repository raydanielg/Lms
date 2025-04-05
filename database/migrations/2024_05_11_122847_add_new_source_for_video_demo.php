<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Edit video_demo_source in webinars Table
        DB::statement("ALTER TABLE `webinars`  MODIFY COLUMN `video_demo_source` enum('upload','youtube','vimeo','external_link','secure_host') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `video_demo`");

        // Edit video_demo_source in bundles Table
        DB::statement("ALTER TABLE `bundles` MODIFY COLUMN `video_demo_source` enum('upload','youtube','vimeo','external_link','secure_host') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `video_demo`");

        // Edit video_demo_source in bundles Table
        DB::statement("ALTER TABLE `upcoming_courses` MODIFY COLUMN `video_demo_source` enum('upload','youtube','vimeo','external_link','secure_host') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `video_demo`");;

    }
};
