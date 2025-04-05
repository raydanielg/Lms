<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_specifications', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->enum('input_type', \App\Models\ProductSpecification::$inputTypes);
        });

        Schema::create('product_specification_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('product_specification_id');
            $table->string('locale', 191)->index();
            $table->string('title');

            $table->foreign('product_specification_id', 'product_specification_id')->on('product_specifications')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_specifications');
        Schema::dropIfExists('product_specification_translations');
    }
}
