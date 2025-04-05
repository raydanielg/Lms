<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebinarChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_chapters', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('webinar_id')->unsigned();
            $table->enum('type', \App\Models\WebinarChapter::$chapterTypes)->default('file');
            $table->string('title');
            $table->integer('order')->nullable()->default(null);
            $table->enum('status', \App\Models\WebinarChapter::$chapterStatus)->default('active');
            $table->integer('created_at')->unsigned();

            $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');
            $table->foreign('webinar_id')->on('webinars')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webinar_chapters');
    }
}
