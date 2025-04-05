<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseNoticeboardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_noticeboards', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->integer('webinar_id')->unsigned();
            $table->enum('color', \App\Models\CourseNoticeboard::$colors);
            $table->string('title');
            $table->text('message');
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('creator_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('webinar_id')->on('webinars')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_noticeboards');
    }
}
