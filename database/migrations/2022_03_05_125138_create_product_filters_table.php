<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_filters', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('category_id')->unsigned();

            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade');
        });

        Schema::create('product_filter_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('product_filter_id');
            $table->string('locale', 191)->index();
            $table->string('title');

            $table->foreign('product_filter_id', 'product_filter_id')->on('product_filters')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_filters');
        Schema::dropIfExists('product_filter_translations');
    }
}
