<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSpecificationMultiValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_specification_multi_values', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('specification_id')->unsigned();

            $table->foreign('specification_id')->references('id')->on('product_specifications')->cascadeOnDelete();
        });

        Schema::create('product_specification_multi_value_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('product_specification_multi_value_id');
            $table->string('locale', 191)->index();
            $table->string('title');

            $table->foreign('product_specification_multi_value_id', 'product_specification_multi_value_id')->on('product_specification_multi_values')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_specification_multi_values');
        Schema::dropIfExists('product_specification_multi_value_translations');
    }
}
