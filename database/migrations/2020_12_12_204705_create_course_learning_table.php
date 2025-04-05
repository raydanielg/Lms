<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseLearningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_learning', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('text_lesson_id')->unsigned()->nullable();
            $table->integer('file_id')->unsigned()->nullable();
            $table->integer('session_id')->unsigned()->nullable();
            $table->integer('created_at')->unsigned();

            $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');
            $table->foreign('text_lesson_id')->on('text_lessons')->references('id')->onDelete('cascade');
            $table->foreign('file_id')->on('files')->references('id')->onDelete('cascade');
            $table->foreign('session_id')->on('sessions')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_learning');
    }
}
