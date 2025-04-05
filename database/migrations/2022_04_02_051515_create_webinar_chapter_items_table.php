<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebinarChapterItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_chapter_items', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->integer('chapter_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->enum('type', ['file', 'session', 'text_lesson', 'quiz']);
            $table->integer('order')->unsigned()->nullable();
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('user_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('chapter_id')->on('webinar_chapters')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webinar_chapter_items');
    }
}
