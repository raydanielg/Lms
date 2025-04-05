<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->integer('forum_id')->unsigned();
            $table->string('slug')->unique()->index();
            $table->string('title');
            $table->text('description');
            $table->boolean('pin')->default(false);
            $table->boolean('close')->default(false);
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('creator_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('forum_id')->on('forums')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forum_topics');
    }
}
