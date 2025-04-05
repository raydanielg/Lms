<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditAndAddTypesToWebinarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webinars', function (Blueprint $table) {
            DB::statement("
                ALTER TABLE `webinars` 
                MODIFY COLUMN `category_id` int(0) UNSIGNED NULL AFTER `creator_id`,
                MODIFY COLUMN `start_date` int(0) NULL AFTER `slug`,
                MODIFY COLUMN `price` int(0) UNSIGNED NULL AFTER `capacity`,
                MODIFY COLUMN `duration` int(0) UNSIGNED NULL AFTER `start_date`,
                MODIFY COLUMN `capacity` int(0) UNSIGNED NULL AFTER `video_demo`,
                MODIFY COLUMN `support` tinyint(1) NULL DEFAULT 0 AFTER `description`,
                MODIFY COLUMN `downloadable` tinyint(1) NULL DEFAULT 0 AFTER `support`,
                MODIFY COLUMN `partner_instructor` tinyint(1) NULL DEFAULT 0 AFTER `downloadable`,
                MODIFY COLUMN `subscribe` tinyint(1) NULL DEFAULT 0 AFTER `partner_instructor`;
            ");

            $table->enum('type', ['webinar', 'course', 'text_lesson'])->after('category_id');
            $table->string('thumbnail')->after('seo_description');
        });
    }
}
