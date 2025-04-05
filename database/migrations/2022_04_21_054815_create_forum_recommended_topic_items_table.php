<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumRecommendedTopicItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_recommended_topic_items', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('recommended_topic_id')->unsigned();
            $table->integer('topic_id')->unsigned();
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('recommended_topic_id')->on('forum_recommended_topics')->references('id')->cascadeOnDelete();
            $table->foreign('topic_id')->on('forum_topics')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forum_recommended_topic_items');
    }
}
