<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_files', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('path');
            $table->integer('order')->unsigned()->nullable();
            $table->enum('status', \App\Models\ProductFile::$fileStatus)->default('active');
            $table->integer('created_at')->unsigned();

            $table->foreign('product_id', 'file_product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('creator_id', 'file_creator_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('product_file_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('product_file_id');
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('description');

            $table->foreign('product_file_id', 'product_file_id')->on('product_files')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_files');
        Schema::dropIfExists('product_file_translations');
    }
}
