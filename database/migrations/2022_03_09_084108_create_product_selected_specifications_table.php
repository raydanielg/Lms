<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSelectedSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_selected_specifications', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('product_specification_id')->unsigned();
            $table->enum('type', \App\Models\ProductSelectedSpecification::$inputTypes);
            $table->boolean('allow_selection')->default(false);
            $table->integer('order')->nullable()->unsigned();
            $table->enum('status', \App\Models\ProductSelectedSpecification::$itemsStatus)->default('active');
            $table->integer('created_at')->unsigned();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');;
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');;
            $table->foreign('product_specification_id')->references('id')->on('product_specifications')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_selected_specifications');
    }
}
