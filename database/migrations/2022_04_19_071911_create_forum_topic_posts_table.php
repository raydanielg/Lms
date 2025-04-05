<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumTopicPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_topic_posts', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('topic_id')->unsigned();
            $table->integer('parent_id')->nullable()->unsigned();
            $table->text('description');
            $table->string('attach')->nullable();
            $table->boolean('pin')->default(false);
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('user_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('topic_id')->on('forum_topics')->references('id')->cascadeOnDelete();
            $table->foreign('parent_id')->on('forum_topic_posts')->references('id')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forum_topic_posts');
    }
}
