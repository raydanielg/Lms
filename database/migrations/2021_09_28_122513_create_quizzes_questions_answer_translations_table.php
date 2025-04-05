<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesQuestionsAnswerTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizzes_questions_answer_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('quizzes_questions_answer_id');
            $table->string('locale', 191)->index();
            $table->string('title')->nullable();

            $table->foreign('quizzes_questions_answer_id', 'quizzes_questions_answer_id')
                ->on('quizzes_questions_answers')
                ->references('id')
                ->onDelete('cascade');
        });

        Schema::table('quizzes_questions_answers', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quizzes_questions_answer_translations');
    }
}
