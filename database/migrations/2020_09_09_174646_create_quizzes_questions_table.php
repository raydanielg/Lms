<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizzes_questions', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('quiz_id')->unsigned();
            $table->integer('creator_user_id')->unsigned();
            $table->string('title');
            $table->string('grade');
            $table->enum('type', ['multiple', 'descriptive']);
            $table->string('correct')->nullable();
            $table->integer('created_at');
            $table->integer('updated_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quizzes_questions');
    }
}
