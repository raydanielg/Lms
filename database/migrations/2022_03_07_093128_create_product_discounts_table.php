<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_discounts', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('name')->nullable();
            $table->integer('percent')->unsigned();
            $table->integer('count')->nullable()->unsigned();
            $table->enum('status', ['active', 'inactive']);
            $table->integer('start_date')->unsigned();
            $table->integer('end_date')->unsigned();
            $table->integer('created_at')->unsigned();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_discounts');
    }
}
