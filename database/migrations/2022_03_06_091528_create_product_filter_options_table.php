<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFilterOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_filter_options', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('filter_id')->unsigned();
            $table->integer('order')->unsigned()->nullable();

            $table->foreign('filter_id')->references('id')->on('product_filters')->onDelete('cascade');;
        });

        Schema::create('product_filter_option_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('product_filter_option_id');
            $table->string('locale', 191)->index();
            $table->string('title');

            $table->foreign('product_filter_option_id', 'product_filter_option_id')->on('product_filter_options')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_filter_options');
        Schema::dropIfExists('product_filter_option_translations');
    }
}
