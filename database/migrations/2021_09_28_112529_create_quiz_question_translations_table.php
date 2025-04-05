<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizQuestionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_question_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('quizzes_question_id');
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('correct')->nullable();

            $table->foreign('quizzes_question_id')->on('quizzes_questions')->references('id')->onDelete('cascade');
        });

        Schema::table('quizzes_questions', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('correct');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_question_translations');
    }
}
