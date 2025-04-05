<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_media', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->enum('type', \App\Models\ProductMedia::$types);
            $table->string('path');
            $table->integer('order')->nullable()->unsigned();
            $table->integer('created_at')->unsigned();

            $table->foreign('product_id', 'media_product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('creator_id', 'media_creator_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_media');
    }
}
