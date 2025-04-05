<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTextLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('text_lessons', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->integer('webinar_id')->unsigned();
            $table->string('title');
            $table->string('image')->nullable();
            $table->integer('study_time')->unsigned()->nullable();
            $table->text('summary');
            $table->longText('content');
            $table->enum('accessibility', ['free', 'paid'])->default('free');
            $table->integer('created_at')->unsigned();
            $table->integer('updated_at')->unsigned()->nullable();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('text_lessons');
    }
}
