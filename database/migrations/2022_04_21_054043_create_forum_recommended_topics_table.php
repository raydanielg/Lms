<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumRecommendedTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_recommended_topics', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->string('title');
            $table->string('icon');
            $table->bigInteger('created_at')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forum_recommended_topics');
    }
}
