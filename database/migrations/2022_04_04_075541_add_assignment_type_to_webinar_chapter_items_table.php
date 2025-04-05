<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddAssignmentTypeToWebinarChapterItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webinar_chapter_items', function (Blueprint $table) {
            DB::statement("ALTER TABLE `webinar_chapter_items` MODIFY COLUMN `type` enum('file', 'session', 'text_lesson', 'quiz', 'assignment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `item_id`");
        });
    }
}
