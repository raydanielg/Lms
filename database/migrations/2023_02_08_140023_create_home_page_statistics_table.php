<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomePageStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_page_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('icon');
            $table->string('color');
            $table->integer('count')->unsigned();
            $table->integer('order')->nullable()->unsigned();
            $table->bigInteger('created_at')->unsigned();
        });

        Schema::create('home_page_statistic_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('home_page_statistic_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('description');

            $table->foreign('home_page_statistic_id', 'home_page_statistic_id')->on('home_page_statistics')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('home_page_statistics');
    }
}
