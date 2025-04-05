<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReviwesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_reviews', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('webinar_id')->unsigned();
            $table->integer('creator_user_id')->unsigned();
            $table->integer('content_quality')->unsigned();
            $table->integer('instructor_skills')->unsigned();
            $table->integer('purchase_worth')->unsigned();
            $table->integer('support_quality')->unsigned();
            $table->integer('rates')->unsigned();
            $table->text('description')->nullable();
            $table->integer('created_at')->unsigned();
            $table->enum('status', ['pending', 'active']);

            $table->foreign('creator_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webinar_reviews');
    }
}
