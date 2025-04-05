<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeSomeColumnVarcharToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_translations', function (Blueprint $table) {
            DB::statement("ALTER TABLE `quiz_translations`
                MODIFY COLUMN `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `locale`");
        });

        Schema::table('quizzes_questions', function (Blueprint $table) {
            DB::statement("ALTER TABLE `quizzes_questions`
                MODIFY COLUMN `image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `type`,
                MODIFY COLUMN `video` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `image`");
        });

        Schema::table('quiz_question_translations', function (Blueprint $table) {
            DB::statement("ALTER TABLE `quiz_question_translations` MODIFY COLUMN `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `locale`");
        });

        Schema::table('quizzes_questions_answers', function (Blueprint $table) {
            DB::statement("ALTER TABLE `quizzes_questions_answers`
                MODIFY COLUMN `image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `question_id`");
        });

        Schema::table('quizzes_questions_answer_translations', function (Blueprint $table) {
            DB::statement("ALTER TABLE `quizzes_questions_answer_translations`
                MODIFY COLUMN `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `locale`");
        });

        Schema::table('webinar_assignment_translations', function (Blueprint $table) {
            DB::statement("ALTER TABLE `webinar_assignment_translations`
                MODIFY COLUMN `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `webinar_assignment_id`;");
        });


    }
}
