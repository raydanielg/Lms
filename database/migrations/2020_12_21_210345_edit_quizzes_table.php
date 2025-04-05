<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            DB::statement("ALTER TABLE `quizzes` 
                MODIFY COLUMN `time` int(0) NULL DEFAULT 0 AFTER `webinar_title`,
                MODIFY COLUMN `attempt` int(0) NULL DEFAULT NULL AFTER `time`");

            DB::statement("ALTER TABLE `quizzes` 
                    MODIFY COLUMN `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `creator_id`;");

            DB::statement("ALTER TABLE `quizzes_questions_answers` 
                MODIFY COLUMN `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `question_id`;");
        });
    }
}
