<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseForumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_forums', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('webinar_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('title');
            $table->text('description');
            $table->string('attach')->nullable();
            $table->boolean('pin')->default(false);
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('webinar_id')->on('webinars')->references('id')->cascadeOnDelete();
            $table->foreign('user_id')->on('users')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_forums');
    }
}
