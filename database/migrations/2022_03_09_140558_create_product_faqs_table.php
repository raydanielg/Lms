<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_faqs', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('order')->unsigned()->nullable();
            $table->integer('created_at')->unsigned();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('creator_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('product_faq_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('product_faq_id');
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('answer');

            $table->foreign('product_faq_id', 'product_faq_id')->on('product_faqs')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_faqs');
    }
}
