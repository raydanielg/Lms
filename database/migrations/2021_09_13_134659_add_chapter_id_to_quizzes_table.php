<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChapterIdToQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->integer('chapter_id')->after('creator_id')->nullable()->unsigned();

            $table->foreign('chapter_id')->on('webinar_chapters')->references('id')->onDelete('cascade');
        });
    }
}
