<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebinarChapterTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_chapter_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('webinar_chapter_id');
            $table->string('locale', 191)->index();
            $table->string('title');

            $table->foreign('webinar_chapter_id', 'webinar_chapter_id')->on('webinar_chapters')->references('id')->onDelete('cascade');
        });

        Schema::table('webinar_chapters', function (Blueprint $table) {
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
        Schema::dropIfExists('webinar_chapter_translations');
    }
}
