<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSelectedSpecificationMultiValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_selected_specification_multi_values', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('selected_specification_id')->unsigned();
            $table->integer('specification_multi_value_id')->unsigned();

            $table->foreign('selected_specification_id','selected_specification_id')->references('id')->on('product_selected_specifications')->cascadeOnDelete();
            $table->foreign('specification_multi_value_id','specification_multi_value_id')->references('id')->on('product_specification_multi_values')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_selected_specification_multi_values');
    }
}
