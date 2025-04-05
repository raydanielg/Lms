<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTextLessonTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('text_lesson_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('text_lesson_id');
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('summary');
            $table->longText('content');

            $table->foreign('text_lesson_id', 'text_lesson_id')->on('text_lessons')->references('id')->onDelete('cascade');
        });

        Schema::table('text_lessons', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('summary');
            $table->dropColumn('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('text_lesson_translations');
    }
}
