<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_badges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('icon');
            $table->string('color');
            $table->string('background');
            $table->bigInteger('start_at')->unsigned()->nullable();
            $table->bigInteger('end_at')->unsigned()->nullable();
            $table->boolean('enable')->default(false);
            $table->bigInteger('created_at')->unsigned();
        });

        Schema::create('product_badge_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_badge_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');

            $table->foreign('product_badge_id')->on('product_badges')->references('id')->cascadeOnDelete();
        });

        Schema::create('product_badge_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_badge_id')->unsigned();
            $table->integer('targetable_id')->unsigned();
            $table->string('targetable_type');

            $table->foreign('product_badge_id')->on('product_badges')->references('id')->cascadeOnDelete();
        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_badges');
        Schema::dropIfExists('product_badge_translations');
        Schema::dropIfExists('product_badge_contents');
    }
};
