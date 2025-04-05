<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->enum('type', \App\Models\Product::$productTypes)->index();
            $table->string('slug')->index();
            $table->integer('category_id')->unsigned()->nullable();
            $table->bigInteger('price')->unsigned()->nullable();
            $table->bigInteger('point')->unsigned()->nullable();
            $table->boolean('unlimited_inventory')->default(false);
            $table->boolean('ordering')->default(false);
            $table->integer('inventory')->unsigned()->nullable();
            $table->integer('inventory_warning')->unsigned()->nullable();
            $table->bigInteger('inventory_updated_at')->unsigned()->nullable();
            $table->bigInteger('delivery_fee')->unsigned()->nullable();
            $table->integer('delivery_estimated_time')->unsigned()->nullable();
            $table->text('message_for_reviewer')->nullable();
            $table->integer('tax')->unsigned()->nullable();
            $table->integer('commission')->unsigned()->nullable();
            $table->enum('status', \App\Models\Product::$productStatus);
            $table->bigInteger('updated_at')->unsigned();
            $table->bigInteger('created_at')->unsigned();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade');
        });

        Schema::create('product_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('product_id');
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('seo_description')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();

            $table->foreign('product_id', 'product_id')->on('products')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_translations');
    }
}
