<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSelectedFilterOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_selected_filter_options', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('filter_option_id')->unsigned();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');;
            $table->foreign('filter_option_id')->references('id')->on('product_filter_options')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_selected_filter_options');
    }
}
