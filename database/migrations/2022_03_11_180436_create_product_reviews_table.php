<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('creator_id')->unsigned();
            $table->integer('product_quality')->unsigned();
            $table->integer('purchase_worth')->unsigned();
            $table->integer('delivery_quality')->unsigned();
            $table->integer('seller_quality')->unsigned();
            $table->char('rates', 10);
            $table->text('description')->nullable();
            $table->integer('created_at')->unsigned();
            $table->enum('status', ['pending', 'active']);

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
        Schema::dropIfExists('product_reviews');
    }
}
